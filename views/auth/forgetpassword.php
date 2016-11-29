<div class="container detail-page">

	<div class="row">
		<div class="col-xs-12">
			<div class="margin-top-15">
				<?php if (isset($errors) && $this->input->post(NULL, TRUE)) { ?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<?php echo $errors; ?>
				</div>
				<?php } ?>
				<?php if (isset($success)) { ?>
					<div class="alert alert-success" role="alert">
					<?php echo $success; ?>
				</div>
				<?php } else { ?>
				<?php 
					$attributes = array('id' => 'forgetpasswordForm');
					echo form_open('auth/forgetpassword' , $attributes); 
				?>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="form-group">
								<label for="loginname">Email</label>
								<input type="text" id="email" name="email" value="<?php echo set_value('email'); ?>" class="form-control" placeholder="Masukkan email yang pernah kamu daftarkan">
							</div>
							<input type="submit" class="btn btn-travelnego" value="Submit" id="submitformbtn">
						</div>
					</div>
				<?php echo form_close(); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="modal fade confirm-email" id="confirm-progress" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
    	<div class="modal-header">
			Pemberitahuan
		</div>
		<div class="modal-body">
			<div>Proses validasi sedang berlangsung , harap tunggu sebentar.</div>
			<div class="btn-wrapper">
				<a href="#" class="btn-yes">Ok</a>
			</div>
		</div>      
    </div>
  </div>
</div>
