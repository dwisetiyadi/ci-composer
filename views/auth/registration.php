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
					<br>
					<a href="<?php echo site_url($this->session->last_page(1)); ?>" class="btn btn-primary">kembali ke halaman sebelumnya</a>
				</div>
				<?php } else { ?>
				<?php $user = $this->mongodb->users->findOne(array('fb_id' => $this->session->loggedIn->fb_id)); ?>
				<?php echo form_open('auth/registration'); ?>
					<div class="panel panel-default">
						<div class="panel-body">
							<p>Mohon melengkapi data pendaftaran Anda sebagai member</p>
							<?php if (!isset($user['email'])) { ?>
							<div class="form-group">
								<label for="loginemail">Email</label>
								<input type="email" name="email" value="<?php echo set_value('email'); ?>" class="form-control" id="loginemail" placeholder="Email" required>
							</div>
							<?php } ?>
							<?php if (!isset($user['password'])) { ?>
							<div class="form-group">
								<label for="loginpassword">Kata Sandi</label>
								<input type="password" name="password" value="<?php echo set_value('password'); ?>" class="form-control" id="loginpassword" placeholder="Password" required>
							</div>
							<?php } ?>
							<?php if (!isset($user['phone'])) { ?>
							<div class="form-group">
								<label for="loginphone">HP</label>
								<input type="number" name="phone" value="<?php echo set_value('phone'); ?>" class="form-control" id="loginphone" placeholder="ex: 081312348765" required>
							</div>
							<?php } ?>
						</div>
						<input type="submit" class="btn btn-travelnego" value="Simpan">
					</div>
				<?php echo form_close(); ?>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
