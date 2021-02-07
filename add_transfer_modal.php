<div class="modal" id="addTransferModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-fw fa-exchange-alt"></i> Transfer Money</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <form action="post.php" method="post" autocomplete="off">
        <div class="modal-body bg-white">

          <ul class="nav nav-pills nav-justified mb-3">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="pill" href="#pills-details">Details</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="pill" href="#pills-assign">Assign</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="pill" href="#pills-notes">Notes</a>
            </li>
          </ul>

          <hr>

          <div class="tab-content">

            <div class="tab-pane fade show active" id="pills-details">

              <div class="form-row">
                
                <div class="form-group col">
                  <label>Date <strong class="text-danger">*</strong></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                    </div>
                    <input type="date" class="form-control" name="date" value="<?php echo date("Y-m-d"); ?>" required>
                  </div>
                </div>
                
                <div class="form-group col">
                  <label>Amount <strong class="text-danger">*</strong></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-fw fa-dollar-sign"></i></span>
                    </div>
                    <input type="number" class="form-control" step="0.01" min="0" name="amount" placeholder="Amount to transfer" required autofocus>
                  </div>
                </div>
              
              </div>
           
              <div class="form-group">
                <label>Transfer <strong class="text-danger">*</strong></label>
                <div class="input-group"> 
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-piggy-bank"></i></span>
                  </div> 
                  <select class="form-control select2" name="account_from" required>
                    <option value="">- Account From -</option>
                    <?php 
                    
                    $sql = mysqli_query($mysqli,"SELECT * FROM accounts WHERE account_archived_at IS NULL AND company_id = $session_company_id ORDER BY account_name ASC"); 
                    while($row = mysqli_fetch_array($sql)){
                      $account_id = $row['account_id'];
                      $account_name = $row['account_name'];
                      $opening_balance = $row['opening_balance'];
                      
                      $sql_payments = mysqli_query($mysqli,"SELECT SUM(payment_amount) AS total_payments FROM payments WHERE account_id = $account_id");
                      $row = mysqli_fetch_array($sql_payments);
                      $total_payments = $row['total_payments'];
                      
                      $sql_revenues = mysqli_query($mysqli,"SELECT SUM(revenue_amount) AS total_revenues FROM revenues WHERE account_id = $account_id");
                      $row = mysqli_fetch_array($sql_revenues);
                      $total_revenues = $row['total_revenues'];

                      $sql_expenses = mysqli_query($mysqli,"SELECT SUM(expense_amount) AS total_expenses FROM expenses WHERE account_id = $account_id");
                      $row = mysqli_fetch_array($sql_expenses);
                      $total_expenses = $row['total_expenses'];

                      $balance = $opening_balance + $total_payments + $total_revenues - $total_expenses;
                    
                    ?>
                      <option <?php if($config_default_transfer_from_account == $account_id){ echo "selected"; } ?> value="<?php echo $account_id; ?>"><?php echo $account_name; ?> [$<?php echo number_format($balance,2); ?>]</option>
                    
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-arrow-right"></i></span>
                  </div>
                  <select class="form-control select2" name="account_to" required>
                    <option value="">- Account To -</option>
                    <?php 
                    
                    $sql = mysqli_query($mysqli,"SELECT * FROM accounts WHERE account_archived_at IS NULL AND company_id = $session_company_id ORDER BY account_name ASC"); 
                    while($row = mysqli_fetch_array($sql)){
                      $account_id = $row['account_id'];
                      $account_name = $row['account_name'];
                      $opening_balance = $row['opening_balance'];
                    
                      $sql_payments = mysqli_query($mysqli,"SELECT SUM(payment_amount) AS total_payments FROM payments WHERE account_id = $account_id");
                      $row = mysqli_fetch_array($sql_payments);
                      $total_payments = $row['total_payments'];

                      $sql_revenues = mysqli_query($mysqli,"SELECT SUM(revenue_amount) AS total_revenues FROM revenues WHERE account_id = $account_id");
                      $row = mysqli_fetch_array($sql_revenues);
                      $total_revenues = $row['total_revenues'];
                      
                      $sql_expenses = mysqli_query($mysqli,"SELECT SUM(expense_amount) AS total_expenses FROM expenses WHERE account_id = $account_id");
                      $row = mysqli_fetch_array($sql_expenses);
                      $total_expenses = $row['total_expenses'];

                      $balance = $opening_balance + $total_payments + $total_revenues - $total_expenses;

                    ?>
                      <option <?php if($config_default_transfer_to_account == $account_id){ echo "selected"; } ?> value="<?php echo $account_id; ?>"><?php echo $account_name; ?> [$<?php echo number_format($balance,2); ?>]</option>
                    
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="pills-assign">

              <?php

              //UNFINISHED BUSINESS (The goal is to select checks that need deposited and autocalculate)

              $sql_payments = mysqli_query($mysqli,"SELECT * FROM payments WHERE account_id = 1");

              while($row = mysqli_fetch_array($sql_payments)){
                $payment_id = $row['payment_id'];
                $payment_method = $row['payment_method'];
                $payment_amount = $row['payment_amount'];
              ?>

                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="payments[]" value="<?php echo $payment_id; ?>">
                  <label class="form-check-label"><?php echo "$payment_method - $payment_amount"; ?></label>
                </div>

              <?php
              }
              ?>

            </div>

            <div class="tab-pane fade" id="pills-notes">
              
              <div class="form-group">
                <textarea class="form-control" rows="8" name="notes"></textarea>
              </div>

            </div>

          </div>

        </div>

        <div class="modal-footer bg-white">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="add_transfer" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>