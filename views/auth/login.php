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
				<?php echo form_open('auth/login'); ?>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="form-group">
								<label for="loginemail">Email</label>
								<input type="email" name="email" value="<?php echo set_value('email'); ?>" class="form-control" id="loginemail" placeholder="Email" required>
							</div>
							<div class="form-group">
								<label for="loginpassword">Kata Sandi</label>
								<input type="password" name="password" value="<?php echo set_value('password'); ?>" class="form-control" id="loginpassword" placeholder="Password" required>
							</div>
							<input type="submit" class="btn btn-travelnego" value="Login">
						</div>
					</div>
				<?php echo form_close(); ?>
				<?php } ?>
				<div class="text-right forget-password-link">
					<a href="<?php echo base_url(); ?>auth/forgetpassword">Lupa Password?</a>
				</div>
			</div>
		</div>
	</div>	
</div>
