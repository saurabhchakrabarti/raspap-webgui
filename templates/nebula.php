  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col">
              <i class="fas fa-key fa-fw mr-2"></i><?php echo _("Nebula"); ?>
            </div>
            <div class="col">
              <button class="btn btn-light btn-icon-split btn-sm service-status float-right">
                <span class="icon text-gray-600"><i class="fas fa-circle service-status-<?php echo $serviceStatus ?>"></i></span>
                <span class="text service-status">nebula <?php echo _($serviceStatus) ?></span>
              </button>
            </div>
          </div><!-- /.row -->
        </div><!-- /.card-header -->
        <div class="card-body">
        <?php $status->showMessages(); ?>
          <form role="form" action="nebula_conf" enctype="multipart/form-data" method="POST">
            <?php echo CSRFTokenFieldTag() ?>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link active" id="clienttab" href="#nebulaclient" data-toggle="tab"><?php echo _("Client settings"); ?></a></li>
                <li class="nav-item"><a class="nav-link" id="logoutputtab" href="#nebulalogoutput" data-toggle="tab"><?php echo _("Logfile output"); ?></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="nebulaclient">
                <h4 class="mt-3"><?php echo _("Client settings"); ?></h4>
                <div class="row">
                  <div class="col">
                   <div class="row">
                      <div class="form-group col-lg-12">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" name="customFile" id="customFile">
                          <label class="custom-file-label" for="customFile"><?php echo _("Select Nebula configuration file (.zip)"); ?></label>
                        </div>
                      </div>
                    </div>
                  </div><!-- col-->
                </div><!-- main row -->
              </div>
              <div class="tab-pane fade" id="nebulalogoutput">
                <h4 class="mt-3"><?php echo _("Client log"); ?></h4>
                <div class="row">
                  <div class="form-group col-md-8">
                    <?php
                        echo '<textarea class="logoutput"></textarea>';
                    ?>
                  </div>
                </div>
              </div>
              <?php if (!RASPI_MONITOR_ENABLED) : ?>
                  <input type="submit" class="btn btn-outline btn-primary" name="SaveNebulaSettings" value="Save settings" />
                  <?php if ($nebulastatus[0] == 0) {
                      echo '<input type="submit" class="btn btn-success" name="StartNebula" value="Start Nebula" />' , PHP_EOL;
                    } else {
                      echo '<input type="submit" class="btn btn-warning" name="StopNebula" value="Stop Nebula" />' , PHP_EOL;
                    }
                  ?>
              <?php endif ?>
              </form>
            </div>
        </div><!-- /.card-body -->
    <div class="card-footer"> Information provided by nebula</div>
  </div><!-- /.card -->
</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

