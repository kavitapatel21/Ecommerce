<?php
/*
Template Name: mailform
Template Post Type: post, page, my-post-type;
*/
get_header();
?>

<?php

/**$subject='Registration Form';
$fname=$_POST['firstname'];
$lname=$_POST['lastname'];
$dob=$_POST['birthdate'];
$radio=$_POST['inlineRadioOptions'];
$phn=$_POST['phoneno'];
$comment=$_POST['comment'];
$to = $_POST['email'];
$body  = 'From: Sixteen clothing' ;
$body .="<html>
<body>
<h3>Fistname: $fname</h3>
<h3>Lastname: $lname</h3>
<h3>DOB: $dob</h3>
<h3>Gender: $radio</h3>
<h3>Phone No: $phn</h3>
<h3>Comment: $comment</h3>
<body>
</html>";
 //$file= get_stylesheet_directory() . '/template/mailform.php';//Template File Path
 //$msg=file_get_contents($file);
 $headers = array('Content-Type: text/html; charset=UTF-8','From: kavita <kavita@plutustec.com>');
 
 if(isset($_POST['submit']))
{
	$mail=wp_mail( $to, $subject, $body, $headers );
}*/
?>
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
                <input class="btn btn-primary btn-lg" id="submit" type="button" value="Submit" name="submit" />
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</section>

<?php
get_footer();
?>