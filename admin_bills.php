<!DOCTYPE html>
<?php
include('db.php');
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: adminLogin.php');
  exit();
}

function readXlsx($filepath) {
  $zip = new ZipArchive();
  if ($zip->open($filepath) !== true) return false;
  $ss = [];
  $sxml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
  if ($sxml) { foreach ($sxml->si as $si) { $ss[] = (string)$si->t; } }
  $rows = [];
  $sxml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
  if ($sxml) {
    $ns = $sxml->getNamespaces(true);
    foreach ($sxml->sheetData->row as $row) {
      $rd = [];
      foreach ($row->c as $cell) {
        $v = (string)$cell->v;
        $rd[] = ((string)$cell['t'] === 's' && isset($ss[(int)$v])) ? $ss[(int)$v] : $v;
      }
      $rows[] = $rd;
    }
  }
  $zip->close();
  return $rows;
}

if (isset($_POST['mark_paid'])) {
  $cid = intval($_POST['cust_id']);
  $upd = "UPDATE customer SET status='Paid' WHERE cust_id='$cid'";
  try { $conn->exec($upd); $success_msg = "Bill #$cid marked as Paid!"; } catch (Exception $e) { $error_msg = "Update failed: " . $e->getMessage(); }
}

if (isset($_POST['update_bill'])) {
  $cid = intval($_POST['cust_id']);
  $pr = floatval($_POST['pr_reading']);
  $cr = floatval($_POST['cr_reading']);
  $tr = $cr - $pr;
  $amt = $tr * 24.00;
  $due = $_POST['due_date'];
  $month = date('F', strtotime($_POST['billing_month'] . '-01'));
  $upd = "UPDATE customer SET PrReading='$pr', CReading='$cr', TReading='$tr', amount='$amt', due_date='$due', billing_month='$month' WHERE cust_id='$cid'";
  try { $conn->exec($upd); $success_msg = "Bill updated!"; } catch (Exception $e) { $error_msg = "Update failed: " . $e->getMessage(); }
}

if (isset($_POST['add_customer'])) {
  $name = $_POST['cust_name'];
  $acct = $_POST['cust_account'];
  $addr = $_POST['cust_address'];
  $pr = floatval($_POST['pr_reading']);
  $cr = floatval($_POST['cr_reading']);
  $tr = $cr - $pr;
  $amt = $tr * 24.00;
  $due = $_POST['due_date'];
  $raw_month = $_POST['billing_month'];
  $month = date('F', strtotime($raw_month . '-01'));
  $ins = "INSERT INTO customer (cust_name, cust_account, cust_address, PrReading, CReading, TReading, amount, due_date, billing_month) VALUES ('$name','$acct','$addr','$pr','$cr','$tr','$amt','$due','$month')";
  try { $conn->exec($ins); $success_msg = "Customer '$name' added!"; } catch (Exception $e) { $error_msg = "Add failed: " . $e->getMessage(); }
}

if (isset($_POST['import_excel']) && isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
  $file = $_FILES['excel_file']['tmp_name'];
  $rows = readXlsx($file);
  if ($rows === false) {
    $error_msg = "Could not read the Excel file.";
  } elseif (count($rows) < 2) {
    $error_msg = "Excel file must have a header row and at least one data row.";
  } else {
    $imported = 0; $errors = [];
    for ($i = 1; $i < count($rows); $i++) {
      $r = $rows[$i];
      if (count($r) < 3) { $errors[] = "Row " . ($i + 1) . ": insufficient columns"; continue; }
      $name = $r[0] ?? '';
      $acct = $r[1] ?? '';
      $addr = $r[2] ?? '';
      $pr = floatval($r[3] ?? 0);
      $cr = floatval($r[4] ?? 0);
      $tr = $cr - $pr;
      $amt = $tr * 24.00;
      $due = $r[6] ?? date('Y-m-d');
      $month = $r[7] ?? date('F');
      if (empty($name) || empty($acct)) { $errors[] = "Row " . ($i + 1) . ": name and account are required"; continue; }
      $ins = "INSERT INTO customer (cust_name, cust_account, cust_address, PrReading, CReading, TReading, amount, due_date, billing_month) VALUES ('$name','$acct','$addr','$pr','$cr','$tr','$amt','$due','$month')";
      try { $conn->exec($ins); $imported++; } catch (Exception $e) { $errors[] = "Row " . ($i + 1) . ": " . $e->getMessage(); }
    }
    $success_msg = "$imported customer(s) imported successfully.";
    if (!empty($errors)) $error_msg = "Errors:<br>" . implode("<br>", array_slice($errors, 0, 10));
  }
}

$customers = $conn->query("SELECT * FROM customer ORDER BY cust_id ASC");
$active_tab = $_GET['tab'] ?? 'list';
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Manage Bills - PrimeWater Quezon Metro</title>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="css/primewater.css">
  <style>
    .tab-pane { padding-top: 20px; }
  </style>
</head>
<body>
  <!-- Utility Bar -->
  <div class="utility-bar clearfix">
    <div class="container">
      <div class="left-links"><a href="index.php"><i class="fas fa-home"></i> PrimeWater</a></div>
      <div class="right-links"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </div>
  </div>

  <!-- Main Header -->
  <header class="main-header">
    <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="admin.php">
          <span class="brand-text"><strong><i class="fas fa-tint text-primary-pw"></i> PrimeWater</strong><small>Admin Panel</small></span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="admin_bills.php">Bills</a></li>
            <li class="nav-item"><a class="nav-link" href="complaintList.php">Complaints</a></li>
            <li class="nav-item"><a class="nav-link" href="applicationList.php">Applications</a></li>
          </ul>
          <div class="nav-icons">
            <span class="user-info">Welcome, <strong><?php echo $_SESSION['user']; ?></strong></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <!-- Page Header -->
  <div class="page-header">
    <div class="container">
      <h2><i class="fas fa-file-invoice-dollar"></i> Bills Management</h2>
      <p>Manage customer bills and meter readings</p>
    </div>
  </div>

  <!-- Content -->
  <div class="section">
    <div class="container">
      <?php if (isset($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show"><?php echo $success_msg; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
      <?php endif; ?>
      <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?php echo $error_msg; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
      <?php endif; ?>

      <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link <?php echo $active_tab === 'list' ? 'active' : ''; ?>" href="?tab=list">Bills Table</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab === 'add' ? 'active' : ''; ?>" href="?tab=add">Add Customer</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab === 'import' ? 'active' : ''; ?>" href="?tab=import">Import Excel</a></li>
      </ul>

      <div class="tab-pane <?php echo $active_tab === 'list' ? 'active' : ''; ?>">
        <div class="card-custom">
          <div class="card-header primary"><i class="fas fa-list"></i> Customer Bills</div>
          <div class="card-body p-0">
            <div class="table-responsive p-3">
              <table id="billsTable" class="table table-striped table-hover" style="width:100%;">
                <thead class="thead-dark" style="background: var(--primary); color: #fff;">
                  <tr>
                    <th>ID</th><th>Name</th><th>Account</th><th>Prev (m&sup3;)</th>
                    <th>Curr (m&sup3;)</th><th>Used (m&sup3;)</th><th>Amount</th>
                    <th>Month</th><th>Due</th><th>Status</th><th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $customers->fetch()): ?>
                  <tr>
                    <td><?php echo $row['cust_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['cust_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['cust_account']); ?></td>
                    <td><?php echo number_format($row['PrReading'], 2); ?></td>
                    <td><?php echo number_format($row['CReading'], 2); ?></td>
                    <td><?php echo number_format($row['TReading'], 2); ?></td>
                    <td>&#8369;<?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['billing_month']); ?></td>
                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                    <td>
                      <?php $status = $row['status'] ?? 'Unpaid'; ?>
                      <span class="badge-pw <?php echo $status === 'Paid' ? 'badge-accomplished' : 'badge-pending'; ?>">
                        <?php echo $status; ?>
                      </span>
                    </td>
                    <td>
                      <?php if ($status === 'Unpaid'): ?>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="cust_id" value="<?php echo $row['cust_id']; ?>">
                        <button type="submit" name="mark_paid" class="btn-pw btn-pw-success btn-pw-sm" onclick="return confirm('Mark bill #<?php echo $row['cust_id']; ?> as paid?')">
                          <i class="fas fa-check"></i> Paid
                        </button>
                      </form>
                      <?php endif; ?>
                      <button class="btn-pw btn-pw-primary btn-pw-sm edit-btn"
                        data-id="<?php echo $row['cust_id']; ?>"
                        data-name="<?php echo htmlspecialchars($row['cust_name']); ?>"
                        data-account="<?php echo htmlspecialchars($row['cust_account']); ?>"
                        data-pr="<?php echo $row['PrReading']; ?>"
                        data-cr="<?php echo $row['CReading']; ?>"
                        data-tr="<?php echo $row['TReading']; ?>"
                        data-amount="<?php echo $row['amount']; ?>"
                        data-due="<?php echo htmlspecialchars($row['due_date']); ?>"
                        data-month="<?php echo htmlspecialchars($row['billing_month']); ?>"
                        data-address="<?php echo htmlspecialchars($row['cust_address']); ?>">
                        <i class="fas fa-edit"></i> Edit
                      </button>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="tab-pane <?php echo $active_tab === 'add' ? 'active' : ''; ?>">
        <div class="card-custom">
          <div class="card-header primary"><i class="fas fa-user-plus"></i> Add New Customer</div>
          <div class="card-body">
            <form method="POST" class="form-custom" id="addCustomerForm">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Customer Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="cust_name" id="addCustName" required>
                </div>
                <div class="form-group col-md-6">
                  <label>Account Number <span class="text-danger">*</span></label>
                  <select class="form-control" name="cust_account" id="addCustAccount" required>
                    <option value="">-- Select account --</option>
                    <?php
                    $accts = $conn->query("SELECT DISTINCT cust_account FROM customer ORDER BY cust_account ASC");
                    while ($a = $accts->fetch()):
                    ?>
                    <option value="<?php echo htmlspecialchars($a['cust_account']); ?>"><?php echo htmlspecialchars($a['cust_account']); ?></option>
                    <?php endwhile; ?>
                    <option value="__new__">+ New Account</option>
                  </select>
                  <input type="text" class="form-control" name="new_account" id="addNewAccount" placeholder="Enter new account number" style="display:none;">
                </div>
              </div>
              <div class="form-group">
                <label>Address</label>
                <textarea class="form-control" name="cust_address" id="addCustAddress" rows="2"></textarea>
              </div>
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label>Prev Reading (m&sup3;)</label>
                  <input type="number" step="0.01" class="form-control add-pr" name="pr_reading" value="0">
                </div>
                <div class="form-group col-md-3">
                  <label>Current Reading (m&sup3;)</label>
                  <input type="number" step="0.01" class="form-control add-cr" name="cr_reading" value="0">
                </div>
                <div class="form-group col-md-3">
                  <label>Consumed (m&sup3;)</label>
                  <input type="number" step="0.01" class="form-control" id="addTrReading" value="0" readonly style="background:#e9ecef;">
                </div>
                <div class="form-group col-md-3">
                  <label>Amount (&#8369;)</label>
                  <input type="number" step="0.01" class="form-control" id="addAmount" value="0" readonly style="background:#e9ecef;">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label>Billing Month</label>
                  <input type="month" class="form-control" name="billing_month">
                </div>
                <div class="form-group col-md-4">
                  <label>Due Date</label>
                  <input type="date" class="form-control" name="due_date">
                </div>
              </div>
              <button type="submit" name="add_customer" class="btn-pw btn-pw-primary"><i class="fas fa-save"></i> Add Customer</button>
            </form>
          </div>
        </div>
      </div>

      <div class="tab-pane <?php echo $active_tab === 'import' ? 'active' : ''; ?>">
        <div class="card-custom">
          <div class="card-header primary"><i class="fas fa-file-excel"></i> Import from Excel</div>
          <div class="card-body">
            <p>Upload a <code>.xlsx</code> file with: <strong>Name, Account, Address, Prev Reading, Curr Reading, Due Date, Billing Month</strong>.</p>
            <form method="POST" enctype="multipart/form-data">
              <div class="upload-area" id="uploadArea" onclick="document.getElementById('excelFile').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <h5 class="mt-2">Click or drag an Excel file here</h5>
                <p class="text-muted mb-0">.xlsx files only</p>
                <input type="file" id="excelFile" name="excel_file" accept=".xlsx" style="display:none" required>
              </div>
              <div id="fileInfo" class="mt-2" style="display:none;"></div>
              <button type="submit" name="import_excel" class="btn-pw btn-pw-primary mt-3"><i class="fas fa-upload"></i> Import</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header" style="background: var(--primary); color: #fff;">
            <h5 class="modal-title">Edit Bill - <span id="modalCustomerName"></span></h5>
            <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="cust_id" id="editCustId">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Account Number</label>
                <input type="text" class="form-control" id="editAccount" readonly>
              </div>
              <div class="form-group col-md-6">
                <label>Address</label>
                <input type="text" class="form-control" id="editAddress" readonly>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Prev Reading (m&sup3;)</label>
                <input type="number" step="0.01" class="form-control" name="pr_reading" id="editPrReading" required>
              </div>
              <div class="form-group col-md-4">
                <label>Current Reading (m&sup3;)</label>
                <input type="number" step="0.01" class="form-control" name="cr_reading" id="editCrReading" required>
              </div>
              <div class="form-group col-md-4">
                <label>Consumed (m&sup3;)</label>
                <input type="number" step="0.01" class="form-control" id="editTrReading" readonly style="background:#e9ecef;">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Amount (&#8369;)</label>
                <input type="number" step="0.01" class="form-control" id="editAmount" readonly style="background:#e9ecef;">
              </div>
              <div class="form-group col-md-4">
                <label>Billing Month</label>
                <input type="month" class="form-control" name="billing_month" id="editBillingMonth" required>
              </div>
              <div class="form-group col-md-4">
                <label>Due Date</label>
                <input type="date" class="form-control" name="due_date" id="editDueDate" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" name="update_bill" class="btn-pw btn-pw-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h5><i class="fas fa-tint"></i> PrimeWater Admin</h5>
          <p style="font-size: 13px; color: rgba(255,255,255,0.5);">Management Panel - Quezon Metro</p>
        </div>
        <div class="col-md-3">
          <h5>Links</h5>
          <a href="admin.php">Dashboard</a>
          <a href="logout.php">Logout</a>
        </div>
        <div class="col-md-3">
          <h5>Contact</h5>
          <p style="font-size: 13px; color: rgba(255,255,255,0.5);">(02) 1234-5678</p>
        </div>
      </div>
      <div class="footer-bottom text-center">
        &copy; <?php echo date('Y'); ?> PrimeWater Quezon Metro. All rights reserved.
      </div>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#billsTable').DataTable();
      $(document).on('click', '.edit-btn', function () {
        var d = $(this).data();
        $('#modalCustomerName').text(d.name);
        $('#editCustId').val(d.id);
        $('#editAccount').val(d.account);
        $('#editAddress').val(d.address);
        $('#editPrReading').val(d.pr);
        $('#editCrReading').val(d.cr);
        $('#editTrReading').val(d.tr);
        $('#editAmount').val(d.amount);
        var m = ['January','February','March','April','May','June','July','August','September','October','November','December'].indexOf(d.month);
        $('#editBillingMonth').val(m !== -1 ? new Date().getFullYear() + '-' + String(m + 1).padStart(2,'0') : '');
        $('#editDueDate').val(d.due);
        calcEditAmount();
        $('#editModal').modal('show');
      });
      function calcEditAmount() {
        var pr = parseFloat($('#editPrReading').val()) || 0;
        var cr = parseFloat($('#editCrReading').val()) || 0;
        var tr = cr - pr;
        $('#editTrReading').val(tr.toFixed(2));
        $('#editAmount').val((tr * 24).toFixed(2));
      }
      $('#editPrReading, #editCrReading').on('input', calcEditAmount);
      function calcAddAmount() {
        var pr = parseFloat($('.add-pr').val()) || 0;
        var cr = parseFloat($('.add-cr').val()) || 0;
        var tr = cr - pr;
        $('#addTrReading').val(tr.toFixed(2));
        $('#addAmount').val((tr * 24).toFixed(2));
      }
      $('.add-pr, .add-cr').on('input', calcAddAmount);

      $('#addCustAccount').on('change', function () {
        var val = $(this).val();
        if (val === '__new__') {
          $('#addNewAccount').show().focus();
          $('#addCustName').val('').prop('readonly', false);
          $('#addCustAddress').val('').prop('readonly', false);
          $('.add-pr').val(0);
          $('#addCustomerForm').attr('data-new', '1');
        } else if (val) {
          $('#addNewAccount').hide().val('');
          $('#addCustomerForm').attr('data-new', '0');
          $.getJSON('api_get_customer.php', { account: val }, function (data) {
            if (data.found) {
              $('#addCustName').val(data.cust_name).prop('readonly', true);
              $('#addCustAddress').val(data.cust_address).prop('readonly', true);
              $('.add-pr').val(parseFloat(data.PrReading) || 0);
            }
          });
        } else {
          $('#addNewAccount').hide().val('');
          $('#addCustName').val('').prop('readonly', false);
          $('#addCustAddress').val('').prop('readonly', false);
          $('.add-pr').val(0);
        }
        calcAddAmount();
      });

      $('#addCustomerForm').on('submit', function () {
        if ($(this).attr('data-new') === '1') {
          var newAcct = $('#addNewAccount').val().trim();
          if (!newAcct) { alert('Please enter a new account number.'); return false; }
          $('#addCustAccount').append($('<option>', { value: newAcct, text: newAcct, selected: true }));
          $('#addCustAccount').val(newAcct);
          $('#addNewAccount').hide();
        }
      });

      $('#excelFile').on('change', function () {
        var f = this.files[0];
        if (f) {
          $('#fileInfo').show().html('<span class="text-success"><i class="fas fa-check-circle"></i> ' + f.name + ' (' + (f.size / 1024).toFixed(1) + ' KB)</span>');
          $('.upload-area').addClass('dragover');
        }
      });
    });
  </script>
</body>
</html>