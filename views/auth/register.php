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
					$attributes = array('id' => 'registerForm');
					echo form_open('auth/register' , $attributes); 
				?>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="form-group">
								<label for="loginname">Nama</label>
								<input type="text" id="name" name="name" value="<?php echo set_value('name'); ?>" class="form-control" id="loginname" placeholder="Nama">
							</div>
							<div class="form-group">
								<label>Kelamin</label>
								<div class="radio">
									<label>
										<input type="radio" name="gender" value="male"<?php echo (set_value('gender') == 'male') ? ' checked' : 'checked'; ?>>
										Laki-laki
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="gender" value="female"<?php echo (set_value('gender') == 'female') ? ' checked' : ''; ?>>
										Perempuan
									</label>
								</div>
							</div>
							<div class="form-group">
								<label for="loginemail">Email</label>
								<input type="email" name="email" value="<?php echo set_value('email'); ?>" class="form-control" id="loginemail" placeholder="Email">
							</div>
							<div class="form-group">
								<label for="loginpassword">Kata Sandi</label>
								<input type="password" name="password" value="<?php echo set_value('password'); ?>" class="form-control" id="loginpassword" placeholder="Password">
							</div>
							<div class="form-group">								
								<div class="form-group phoneGroup">
									<label for="pemesanPhone" class="form-title">Nomor Handphone <span class="visible-xs small">(tanpa tanda baca)</span></label>
									<div>
										<div class="flag-phone-wrapper f16 flag-trigger">
											<span class="flag id"></span>								
										</div>
										<div class="flag-phone-input">
											<input placeholder="Contoh : +6285767111117" type="tel" name="phone" value="<?php if(!empty(set_value('phone'))){ echo set_value('phone'); } else { echo '+62';} ?>" class="form-control" id="pemesanPhone" aria-describedby="helphone" required>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="phone-input-hidden f16">							
										<input type="text" name="countrycode" placeholder="ketikkan nama negara" id="countrycode">
										<input type="hidden" name="hiddencountrycode" id="hiddencountrycode" value="62">
										<div id="country-result"></div>
									</div>
									<!-- <span id="helphone" class="help-block small">Isi dengan angka semua tanpa tanda baca</span> -->
								</div>
							</div>

							<input type="submit" class="btn btn-travelnego" value="Register">
						</div>
					</div>
				<?php echo form_close(); ?>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
