<?php
/*
Template Name: ajaxmailform
Template Post Type: post, page, my-post-type;
*/
get_header();
?>

<script>
     jQuery(document).ready(function() {   
            jQuery('#phoneNumber').change( function() {	 
              //  var getotpcode = '';       
              var otpverify = '';
	            var getotpcode= '';
              var otpemail = '';
              var storemail = '';
                     var fname = jQuery('#firstName').val();
                     var lname = jQuery('#lastName').val();
                     var dob = jQuery('#birthdayDate').val();
                     var email = jQuery('#emailAddress').val();
                     var phno = jQuery('#phoneNumber').val();
                     var otpno = jQuery('#partitioned').val();
                     var comment = jQuery('#exampleFormControlTextarea1').val();
                     var data =  {
			        	    'fname' : fname,
						        'lname' : lname,
						        'dob' : dob,
						        'email' : email,
						        'phno' : phno,
                		'comment' : comment,
                    'otpno' : otpno,
			        };
                $.ajax({
                    type        : 'POST', 
                    url         : "<?php echo get_stylesheet_directory_uri();?>/template/mailotp.php",
                    data        :   data,
                                    action: 'otpverification',
                  //  dataType    : 'json',
                    success: function(data){ 
                            console.log(data);
                           // getotpcode = data;
                            jQuery('#mailotp').val(data);
                            alert('please check your mail for verification');
                    },  
                });    
            });
          });  
        function validateotp(){
          var otpverify = jQuery('#partitioned').val();
	        var getotpcode= jQuery('#mailotp').val();
          // alert(otpverify);
           //  alert(getotpcode);
          if( Number( otpverify ) == Number( getotpcode ) )
              {
		             alert('OTP is correct');  
	            }
	            else{
		            alert('OTP is wrong');
	            }
        }
    </script>
<style>
	.gradient-custom {
  /* fallback for old browsers */
  background: #f093fb;

  /* Chrome 10-25, Safari 5.1-6 */
  background: -webkit-linear-gradient(to bottom right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1));

  /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
  background: linear-gradient(to bottom right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1))
}

.card-registration .select-input.form-control[readonly]:not([disabled]) {
  font-size: 1rem;
  line-height: 2.15;
  padding-left: .75em;
  padding-right: .75em;
}
.card-registration .select-arrow {
  top: 13px;
}
</style>


<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-12 col-lg-9 col-xl-7">
        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
          <div class="card-body p-4 p-md-5">
            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Registration Form</h3>
            <form method="POST" accept-charset="UTF-8">

              <div class="row">
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                    <input type="text" id="firstName" class="form-control form-control-lg" name="firstname" />
                    <label class="form-label" for="firstName">First Name</label>
                  </div>

                </div>
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                    <input type="text" id="lastName" class="form-control form-control-lg" name="lastname" />
                    <label class="form-label" for="lastName">Last Name</label>
                  </div>

                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4 d-flex align-items-center">

                  <div class="form-outline datepicker w-100">
                    <input
                      type="text"
                      class="form-control form-control-lg"
                      id="birthdayDate"
					            name="birthdate"
                    />
                    <label for="birthdayDate" class="form-label">Birthday</label>
                  </div>

                </div>
                <div class="col-md-6 mb-4">

                  <h6 class="mb-2 pb-1">Gender: </h6>

                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="inlineRadioOptions"
                      id="femaleGender"
                      value="Female"
                      checked
                    />
                    <label class="form-check-label" for="femaleGender">Female</label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="inlineRadioOptions"
                      id="maleGender"
                      value="Male"
                    />
                    <label class="form-check-label" for="maleGender">Male</label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="inlineRadioOptions"
                      id="otherGender"
                      value="Other"
                    />
                    <label class="form-check-label" for="otherGender">Other</label>
                  </div>

                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4 pb-2">

                  <div class="form-outline">
                    <input type="email" id="emailAddress" class="form-control form-control-lg" name="email" />
                    <label class="form-label" for="emailAddress">Email</label>
                  </div>

                </div>
                <div class="col-md-6 mb-4 pb-2">

                  <div class="form-outline">
                    <input type="tel" id="phoneNumber" class="form-control form-control-lg" name="phoneno"/>
                    <label class="form-label" for="phoneNumber">Phone Number</label>
                  </div>

                </div>
              </div>

              <div class="col-md-6 mb-4 pb-2">

                  <div class="form-outline">
                    <input id="partitioned" type="text" maxlength="7" />
                    <label class="form-label" for="OTP">OTP</label>
                  </div>

              </div>
              </div>

             <!-- <div class="row">
                <div class="col-12">

                  <select class="select form-control-lg">
                    <option value="1" disabled>Choose option</option>
                    <option value="2">Subject 1</option>
                    <option value="3">Subject 2</option>
                    <option value="4">Subject 3</option>
                  </select>
                  <label class="form-label select-label">Choose option</label>

                </div>
              </div>-->

			  <div class="row">
                <div class="col-12">
			
				<textarea class="form-control form-control-lg" id="exampleFormControlTextarea1" rows="3" name="comment"></textarea>
				<label class="form-label" for="comment">Comment</label>
				
				</div>
			  </div>
			 
              <div class="mt-4 pt-2">
                <input class="btn btn-primary btn-lg" id="submit" type="button" value="Submit" name="submit" onclick="validateotp();" />
              </div>
              
              <input type="hidden" id="mailotp"/>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id=#success></div> 
</section>

<?php
get_footer();
?>