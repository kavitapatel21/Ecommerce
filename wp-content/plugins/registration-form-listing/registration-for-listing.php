<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Table with Add and Delete Row Feature</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>
<?php
/*
Plugin Name: Registration-Form-Listing
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words.
Author: ABC
Version: 1.0
*/

function create_database_data() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   
	// Create the Registration form listing table
	$table_name = $wpdb->prefix .'registrationentry';
	$sql = "CREATE TABLE $table_name (
	id INTEGER(10) AUTO_INCREMENT,
	firstname varchar(255),
	lastname varchar(255),
	birthdate DATE,
	gender varchar(255),
	email varchar(255),
	contactno varchar(255),
	comment varchar(255),
  flag INTEGER NOT NULL,
	PRIMARY KEY (id)
	) $charset_collate;";
	dbDelta( $sql );
   }
   register_activation_hook( __FILE__, 'create_database_data' );

add_action( 'admin_menu', 'admin_menu_page' );

function admin_menu_page(){
	add_menu_page(
		'My Page Title', // page <title>Title</title>
		'admin users', // menu link text
		'manage_options', // capability to access the page
		'data-display', // page URL slug
		'displaydata', // callback function /w content
		'dashicons-star-half', // menu icon
		5 // priority
	);
add_submenu_page( null,//parent page slug
  'employee_delete',//$page_title
  'Employee Delete',// $menu_title
  'manage_options',// $capability
  'data_delete',// $menu_slug,
  'delete_data'// $function
);
add_submenu_page( null,//parent page slug
  'employee_insert',//$page_title
  'Employee Insert',// $menu_title
  'manage_options',// $capability
  'data_insert',// $menu_slug,
  'insert_data'// $function
);
add_submenu_page( null,//parent page slug
  'employee_update',//$page_title
  'Employee Update',// $menu_title
  'manage_options',// $capability
  'data_view',// $menu_slug,
  'view_details'// $function
);
add_submenu_page( null,//parent page slug
'employee_update',//$page_title
'Employee Update',// $menu_title
'manage_options',// $capability
'Employee_Update',// $menu_slug,
'update_data'// $function
);
}
function insert_data(){
  global $wpdb;
  if(isset($_POST['btn_submit'])){
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $dob = $_POST['birthdate'];
    $gender = $_POST['inlineRadioOptions'];
    $email = $_POST['email'];
    $contactno = $_POST['phoneno'];
    $comment = $_POST['comment'];
    $tablename = $wpdb->prefix."registrationentry";
    $insert_sql = "INSERT INTO ".$tablename."(`firstname`, `lastname`, `birthdate`, `gender`, `email`, `contactno`, `comment`,`flag`) 
    values('". $fname ."','". $lname ."','". $dob ."','". $gender ."','". $email ."','". $contactno ."','". $comment ."','1') ";
       $wpdb->query($insert_sql);
      // echo $wpdb->last_query; 
      $home_url = admin_url('admin.php?page=data-display');
      header('Location: ' . $home_url);  
  }
    ?>
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
                    <label class="form-label" for="firstName">First Name</label>
                      <input type="text" id="firstName" class="form-control form-control-lg" name="firstname" />
                     
                    </div>
  
                  </div>
                  <div class="col-md-6 mb-4">
  
                    <div class="form-outline">
                    <label class="form-label" for="lastName">Last Name</label>
                      <input type="text" id="lastName" class="form-control form-control-lg" name="lastname" />
                     
                    </div>
  
                  </div>
                </div>
  
                <div class="row">
                  <div class="col-md-6 mb-4 ">
                  <label for="birthdayDate" class="form-label">DOB</label>
                    <div class="form-outline datepicker w-100">
                      <input
                        type="text"
                        class="form-control form-control-lg"
                        id="birthdayDate"
                        name="birthdate"
                      />
                      
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
                    <label class="form-label" for="emailAddress">Email</label>
                      <input type="email" id="emailAddress" class="form-control form-control-lg" name="email" />
                      
                    </div>
  
                  </div>
                  <div class="col-md-6 mb-4 pb-2">
  
                    <div class="form-outline">
                    <label class="form-label" for="phoneNumber">Phone Number</label>
                      <input type="tel" id="phoneNumber" class="form-control form-control-lg" name="phoneno"/>
                      
                    </div>
  
                  </div>
                </div>
  
                <div class="row">
                  <div class="col-12">
                  <label class="form-label" for="comment">Comment</label>
          <textarea class="form-control form-control-lg" id="exampleFormControlTextarea1" rows="3" name="comment"></textarea>
          </div>
          </div>
      
                <div class="mt-4 pt-2">
                  <input class="btn btn-primary btn-lg" id="submit" type="submit" value="Submit" name="btn_submit"/>
                </div>   
                <!--<input type="hidden" id="mailotp"/>-->
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </section>
  
  <?php }
//show all saved record
function displaydata()
{ ?>
<div class="container" style="margin-top: 20px;">
  <div class="row">
  <?php
         global $wpdb;
         $tablename = $wpdb->prefix."registrationentry";
      $entriesList = $wpdb->get_results("SELECT * FROM ".$tablename." WHERE flag=1");
      $total_rows = $wpdb->num_rows;
      //echo $total_rows;
        if (!isset ($_GET['paged']) ) {  
          $page_number = 1;  
      } else {  
          $page_number = $_GET['paged'];   
      } 
      $limit = 5;  // variable to store the number of rows per page  
      $offset = ($page_number - 1) * $limit;  // get the initial page number
      $total_pages = ceil ($total_rows / $limit);   // get the required number of pages
      //echo $total_pages;
      $columns = array('id','firstname','lastname','email','gender');
      // Only get the column if it exists in the above columns array, if it doesn't exist the database table will be sorted by the first item in the columns array.
      $column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];

      // Get the sort order for the column, ascending or descending, default is ascending.
      $sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

      $getQuery =$wpdb->get_results("SELECT id,firstname,lastname,email,gender FROM wp_registrationentry WHERE  flag=1 order by $column $sort_order LIMIT " . $offset . ',' . $limit);  
     // echo $wpdb->last_query;
       $up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order); 
       //the column name again it will sort in the opposite order.
      $asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
      $add_class = ' class="highlight"';
    ?>
       <div class="col-12">
      <table class="table table-bordered">
        <thead>
          <tr>
          <button type="button" class="btn btn-success" style="margin-bottom: 10px;">
          <a href="<?php echo admin_url('admin.php?page=data_insert&id='); ?>"  style="color: white;">Add Record</button>
          </tr>
          <tr>
         <?php global $wp;
          //echo add_query_arg( $wp->query_vars, home_url( $wp->request ) );?>
            <th scope="col"><a href="<?php echo add_query_arg( $wp->query_vars, home_url( $wp->request ) );?>&column=id&order=<?php echo $asc_or_desc; ?>">id</a></th>
            <th scope="col"><a href="<?php echo add_query_arg( $wp->query_vars, home_url( $wp->request ) );?>&column=firstname&order=<?php echo $asc_or_desc; ?>">Firstname</a></th>
            <th scope="col"><a href="<?php echo add_query_arg( $wp->query_vars, home_url( $wp->request ) );?>&column=lastname&order=<?php echo $asc_or_desc; ?>">Lastname</a></th>
            <th scope="col"><a href="<?php echo add_query_arg( $wp->query_vars, home_url( $wp->request ) );?>&column=email&order=<?php echo $asc_or_desc; ?>">Email</a></th>
            <th scope="col"><a href="<?php echo add_query_arg( $wp->query_vars, home_url( $wp->request ) );?>&column=gender&order=<?php echo $asc_or_desc; ?>">Gender</a></th>
          </tr>
        </thead>
        <?php
        foreach( $getQuery as $entry){
          $id = $entry->id;
          $fname = $entry->firstname;
          $lname = $entry->lastname; 
          $email = $entry->email;
          $gender = $entry->gender;  ;
		   ?> 
        <tbody>
          <tr>
          <td<?php echo $column == 'id' ? $add_class : ''; ?>><?php echo $id;?></td>
            <td<?php echo $column == 'firstname' ? $add_class : ''; ?>><?php echo $fname;?></td>
            <td<?php echo $column == 'lastname' ? $add_class : ''; ?>><?php echo $lname;?></td>
			      <td<?php echo $column == 'email' ? $add_class : ''; ?>><?php echo $email;?></td>
            <td<?php echo $column == 'gender' ? $add_class : ''; ?>><?php echo $gender;?></td>
            <td>
            <button type="button" class="btn btn-danger" name="btndelete">
            <a href="<?php echo admin_url('admin.php?page=data_delete&id=' . $entry->id); ?>" style="color: white;">
             Delete</button>
            </td>
            <td>
            <button type="button" class="btn btn-info" name="btnedit">
            <a href="<?php echo admin_url('admin.php?page=Employee_Update&id=' . $entry->id); ?>" style="color: white;">
              Edit</button></td>
            <td>
            <button type="button" class="btn btn-secondary" name="btnview">
            <a href="<?php echo admin_url('admin.php?page=data_view&id=' . $entry->id); ?>" style="color: white;">
              View Details</button></td>
          </tr>
          <?php } ?>
          </tbody>
      </table>
    </div> 
  </div>
</div> 

<?php 
global $wp_query;

$tag = '<div class="pagination">' ;
$tag .= paginate_links( array(
        'base'              => add_query_arg('paged','%#%'),
        'format'            => '',
        'current'           => $page_number,
        'total'             =>  $total_pages,
        'prev_next'         => True,
        'prev_text'         => __('«'),
        'next_text'         => __('»'),
        'before_page_number' => '<span class="pagenum" style="color:blue;">',
        'after_page_number'  => '</span>'
    ) );

$tag .= '</div>';
echo $tag; 
?>
<?php }  


function delete_data(){
 // echo "employee delete";
  if(isset($_GET['id'])){
      global $wpdb;
      $i=$_GET['id'];
      $execut= $wpdb->query( $wpdb->prepare( "UPDATE wp_registrationentry SET flag = 0 WHERE ID = $i") );
      $wpdb->query($execut);
      echo $wpdb->last_query;
      $home_url = admin_url('admin.php?page=data-display');
      //$home_url="http://localhost/clgpro/wp-admin/admin.php?page=data-display";
      header('Location: ' . $home_url); 
  ?>
  <?php
     // $wpdb->delete(
      //    $table_name,
        //  array('id'=>$i)
     // );
  }
}


function view_details(){
  //"echo "View full details"";
  ?>
  <div class="container" style="margin-top: 20px;">
  <div class="row">
  <?php
  if(isset($_GET['id'])){
    global $wpdb;
    $i=$_GET['id'];
    $tablename = $wpdb->prefix."registrationentry";
    $entriesList = $wpdb->get_results("SELECT * FROM ".$tablename." WHERE id= $i");
    ?>
<div class="col-12">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">Firstname</th>
            <th scope="col">Lastname</th>
            <th scope="col">DOB</th>
            <th scope="col">Gender</th>
            <th scope="col">Email</th>
            <th scope="col">Phone no</th>
			      <th scope="col">Comment</th>
          </tr>
        </thead>
        <?php
        foreach(  $entriesList as $entry){
          $id = $entry->id;
          $fname = $entry->firstname;
          $lname = $entry->lastname;
          $birthdate = $entry->birthdate;
		      $gender = $entry->gender;  
          $email = $entry->email;
		      $phno = $entry->contactno;
		      $comment = $entry->comment;
		   ?> 
        <tbody>
          <tr>
          <td><?php echo $id;?></td>
            <td><?php echo $fname;?></td>
            <td><?php echo $lname;?></td>
            <td><?php echo $birthdate;?></td>
            <td><?php echo $gender;?></td>
			      <td><?php echo $email;?></td>
			      <td><?php echo $phno;?></td>
			      <td><?php echo $comment;?></td> 
            <td>
            <button type="button" class="btn btn-link" name="btnback">
            <a href="<?php echo admin_url('admin.php?page=data-display&id=' . $entry->id); ?>">
             Back</button>
            </td>
          </tr>
          <?php } ?>
          </tbody>
      </table>
    </div> 
  </div>
</div> 
  <?php }
}
  ?>


<?php //Edit record
//echo "update page";
function update_data(){

    $i=$_GET['id'];
    global $wpdb;
    $table_name = $wpdb->prefix . 'registrationentry';
    $employees = $wpdb->get_results("SELECT * from $table_name where id=$i");
    //echo $wpdb->last_query;
   // echo "<pre>";
   // print_r($employees[0]);
  // echo $employees[0]->comment;
    ?>
   <section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
      <div class="row justify-content-center align-items-center h-100">
        <div class="col-12 col-lg-9 col-xl-7">
          <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
            <div class="card-body p-4 p-md-5">
              <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Update Info</h3>
              <form method="POST" accept-charset="UTF-8" id="updform">
  
                <div class="row">
                  <div class="col-md-6 mb-4">
  
                    <div class="form-outline">
                    <label class="form-label" for="firstName">First Name</label>
                      <input type="text" id="updfirstName" class="form-control form-control-lg" name="updfirstname" value="<?= $employees[0]->firstname; ?>" />
                     
                    </div>
  
                  </div>
                  <div class="col-md-6 mb-4">
  
                    <div class="form-outline">
                    <label class="form-label" for="lastName">Last Name</label>
                      <input type="text" id="updlastName" class="form-control form-control-lg" name="updlastname" value="<?= $employees[0]->lastname; ?>"/>
                     
                    </div>
  
                  </div>
                </div>
  
                <div class="row">
                  <div class="col-md-6 mb-4 ">
                  <label for="birthdayDate" class="form-label">DOB</label>
                    <div class="form-outline datepicker w-100">
                      <input
                        type="text"
                        class="form-control form-control-lg"
                        id="updbirthdayDate"
                        name="updbirthdate"
                        value="<?= $employees[0]->birthdate; ?>"
                      />
                      
                    </div>
  
                  </div>
                  <div class="col-md-6 mb-4">
  
                    <h6 class="mb-2 pb-1">Gender: </h6>
  
                    <div class="form-check form-check-inline">
                      <input
                        class="form-check-input"
                        type="radio"
                        name="updinlineRadioOptions"
                        id="updfemaleGender"
                        value="<?= $employees[0]->gender; ?>"
                        checked
                      />
                      <label class="form-check-label" for="femaleGender">Female</label>
                    </div>
  
                    <div class="form-check form-check-inline">
                      <input
                        class="form-check-input"
                        type="radio"
                        name="updinlineRadioOptions"
                        id="updmaleGender"
                        value="<?= $employees[0]->gender; ?>"
                      />
                      <label class="form-check-label" for="maleGender">Male</label>
                    </div>
  
                    <div class="form-check form-check-inline">
                      <input
                        class="form-check-input"
                        type="radio"
                        name="updinlineRadioOptions"
                        id="updotherGender"
                        value="<?= $employees[0]->gender; ?>"
                      />
                      <label class="form-check-label" for="otherGender">Other</label>
                    </div>
  
                  </div>
                </div>
  
                <div class="row">
                  <div class="col-md-6 mb-4 pb-2">
  
                    <div class="form-outline">
                    <label class="form-label" for="emailAddress">Email</label>
                      <input type="email" id="updemailAddress" class="form-control form-control-lg" name="updemail" value="<?= $employees[0]->email; ?>" />
                      
                    </div>
  
                  </div>
                  <div class="col-md-6 mb-4 pb-2">
  
                    <div class="form-outline">
                    <label class="form-label" for="phoneNumber">Phone Number</label>
                      <input type="tel" id="updphoneNumber" class="form-control form-control-lg" name="updphoneno" value="<?= $employees[0]->contactno;?>" />
                      
                    </div>
  
                  </div>
                </div>
  
                <div class="row">
                  <div class="col-12">
                  <label class="form-label" for="comment">Comment</label>
                  <textarea class="form-control form-control-lg" id="exampleFormControlTextarea1" rows="3" name="updcomment" value="<?= $employees[0]->comment; ?>"><?= $employees[0]->comment; ?></textarea>
                 <!-- <input type="text" id="updcomment" class="form-control form-control-lg" name="upcomment" value="<?= $employees[0]->comment; ?>"/>-->
                </div>
                </div>
      
                <div class="mt-4 pt-2">
                  <input class="btn btn-primary btn-lg" id="updsubmit" type="submit" value="Submit" name="btn_update"/>
                </div>   
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </section>
  
    <?php
}
if(isset($_POST['btn_update']))
{
    global $wpdb;
    $table_name=$wpdb->prefix.'registrationentry';
    $i=$_GET['id'];
    $fname = $_POST['updfirstname'];
    $lname = $_POST['updlastname'];
    $dob = $_POST['updbirthdate'];
    $gender = $_POST['updinlineRadioOptions'];
    $email = $_POST['updemail'];
    $contactno = $_POST['updphoneno'];
    $comment = $_POST['updcomment'];
  
    $wpdb->update (
        $table_name,
        array(
            'firstname'=>$fname,
            'lastname'=>$lname,
            'birthdate'=> $dob,
            'gender'=>  $gender,
            'email'=> $email,
            'contactno'=>$contactno,
            'comment'=> $comment,
        ),
        array(
            'id'=>$i,
        )
    );
    echo $wpdb->last_query;
    $home_url = admin_url('admin.php?page=data-display');
    header('Location: ' . $home_url);  
  ?> 
 <?php }
?>
  
  


  