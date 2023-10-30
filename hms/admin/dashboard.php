<?php
session_start();
include('include/config.php');
include('include/checklogin.php');
check_login();

// Fetch total number of patients
$stmtPatients = $conn->prepare("SELECT COUNT(*) as count FROM users");
$stmtPatients->execute();
$resultPatients = $stmtPatients->get_result();
$rowPatients = $resultPatients->fetch_assoc();
$totalPatients = $rowPatients['count'];
$stmtPatients->close();

// Fetch total number of doctors
$stmtDoctors = $conn->prepare("SELECT COUNT(*) as count FROM doctors");
$stmtDoctors->execute();
$resultDoctors = $stmtDoctors->get_result();
$rowDoctors = $resultDoctors->fetch_assoc();
$totalDoctors = $rowDoctors['count'];
$stmtDoctors->close();

// Fetch total number of appointments
$stmtAppointments = $conn->prepare("SELECT COUNT(*) as count FROM appointment");
$stmtAppointments->execute();
$resultAppointments = $stmtAppointments->get_result();
$rowAppointments = $resultAppointments->fetch_assoc();
$totalAppointments = $rowAppointments['count'];
$stmtAppointments->close();

// Fetch the number of appointments for each month in the last year
$stmtMonthlyAppointments = $conn->prepare("SELECT DATE_FORMAT(appointmentDate, '%Y-%m') as month, COUNT(*) as count FROM appointment WHERE DATE(appointmentDate) BETWEEN DATE_SUB(NOW(), INTERVAL 1 YEAR) AND NOW() GROUP BY month");
$stmtMonthlyAppointments->execute();
$resultMonthlyAppointments = $stmtMonthlyAppointments->get_result();
$monthlyAppointments = [];
while ($row = $resultMonthlyAppointments->fetch_assoc()) {
	$monthlyAppointments[$row['month']] = $row['count'];
}
$stmtMonthlyAppointments->close();


// Fetch the monthly revenue for each month in the last year
$stmtMonthlyRevenue = $conn->prepare("SELECT DATE_FORMAT(appointmentDate, '%Y-%m') as month, SUM(consultancyFees) as totalRevenue FROM appointment WHERE DATE(appointmentDate) BETWEEN DATE_SUB(NOW(), INTERVAL 1 YEAR) AND NOW() GROUP BY month");
$stmtMonthlyRevenue->execute();
$resultMonthlyRevenue = $stmtMonthlyRevenue->get_result();
$monthlyRevenue = [];
while ($row = $resultMonthlyRevenue->fetch_assoc()) {
	$monthlyRevenue[$row['month']] = $row['totalRevenue'];
}
$stmtMonthlyRevenue->close();


// Fetch the count of appointments for each specialization
$stmtSpecializationCount = $conn->prepare("SELECT doctorSpecialization, COUNT(*) as count FROM appointment GROUP BY doctorSpecialization");
$stmtSpecializationCount->execute();
$resultSpecializationCount = $stmtSpecializationCount->get_result();
$specializationCounts = [];
while ($row = $resultSpecializationCount->fetch_assoc()) {
	$specializationCounts[$row['doctorSpecialization']] = $row['count'];
}
$stmtSpecializationCount->close();


// Fetch the count of appointments for each hour of the day
$stmtAppointmentTimes = $conn->prepare("SELECT HOUR(appointmentTime) as hour, COUNT(*) as count FROM appointment GROUP BY hour");
$stmtAppointmentTimes->execute();
$resultAppointmentTimes = $stmtAppointmentTimes->get_result();
$appointmentTimes = [];
while ($row = $resultAppointmentTimes->fetch_assoc()) {
    $appointmentTimes[$row['hour']] = $row['count'];
}
$stmtAppointmentTimes->close();


// Fetch the revenue for each specialization
$stmtSpecializationRevenue = $conn->prepare("SELECT doctorSpecialization, SUM(consultancyFees) as revenue FROM appointment GROUP BY doctorSpecialization");
$stmtSpecializationRevenue->execute();
$resultSpecializationRevenue = $stmtSpecializationRevenue->get_result();
$specializationRevenue = [];
while ($row = $resultSpecializationRevenue->fetch_assoc()) {
    $specializationRevenue[$row['doctorSpecialization']] = $row['revenue'];
}
$stmtSpecializationRevenue->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Admin | Dashboard</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta content="" name="description" />
	<meta content="" name="author" />
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
	<link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
	<link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
	<link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
	<link href="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" media="screen">
	<link href="vendor/select2/select2.min.css" rel="stylesheet" media="screen">
	<link href="vendor/bootstrap-datepicker/bootstrap-datepicker3.standalone.min.css" rel="stylesheet" media="screen">
	<link href="vendor/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="assets/css/styles.css">
	<link rel="stylesheet" href="assets/css/plugins.css">
	<link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />


</head>

<body>
	<div id="app">
		<?php include('include/sidebar.php'); ?>
		<div class="app-content">

			<?php include('include/header.php'); ?>

			<!-- end: TOP NAVBAR -->
			<div class="main-content">
				<div class="wrap-content container" id="container">
					<!-- start: PAGE TITLE -->
					<section id="page-title">
						<div class="row">
							<div class="col-sm-8">
								<h1 class="mainTitle">Admin | Dashboard</h1>
							</div>
							<ol class="breadcrumb">
								<li>
									<span>Admin</span>
								</li>
								<li class="active">
									<span>Dashboard</span>
								</li>
							</ol>
						</div>
					</section>
					<!-- end: PAGE TITLE -->
					<!-- start: BASIC EXAMPLE -->
					<div class="container-fluid container-fullw bg-white">
						<div class="row">
							<div class="col-sm-4">
								<div class="panel panel-white no-radius text-center">
									<div class="panel-body">
										<span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-smile-o fa-stack-1x fa-inverse"></i> </span>
										<h2 class="StepTitle">Manage Patients</h2>

										<p class="links cl-effect-1">
											<a href="manage-users.php">
												Total Patients: <?php echo htmlentities($totalPatients); ?>
											</a>

										</p>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-white no-radius text-center">
									<div class="panel-body">
										<span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-users fa-stack-1x fa-inverse"></i> </span>
										<h2 class="StepTitle">Manage Doctors</h2>

										<p class="cl-effect-1">
											<a href="manage-doctors.php">
												Total Doctors: <?php echo htmlentities($totalDoctors); ?>
											</a>

										</p>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-white no-radius text-center">
									<div class="panel-body">
										<span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-terminal fa-stack-1x fa-inverse"></i> </span>
										<h2 class="StepTitle"> Appointments</h2>

										<p class="links cl-effect-1">
											<a href="book-appointment.php">
												<a href="appointment-history.php">
													Total Appointments: <?php echo htmlentities($totalAppointments); ?>
												</a>
											</a>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>






					<!-- end: SELECT BOXES -->

				</div>
			</div>
		</div>


		<div>
								<div class="panel panel-white no-radius text-center">
									<div class="panel-body">
										<!-- <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-users fa-stack-1x fa-inverse"></i> </span> -->
										<h2 class="StepTitle">Number of Appointments over the Months</h2>

										<div style="max-width: 700px; margin: 50px auto;">
											<canvas id="appointmentsChart"></canvas>
										</div>
									</div>
								</div>
							
		

		

							<div class="panel panel-white no-radius text-center">
									<div class="panel-body">
										<!-- <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-users fa-stack-1x fa-inverse"></i> </span> -->
										<h2 class="StepTitle">Revenue Generated Over Each Month</h2>

										<!-- Inserting the graph canvas for revenue -->
										<div style="max-width: 700px; margin: 50px auto;">
											<canvas id="revenueChart"></canvas>
										</div>
									</div>
								</div>
							

	

								

								<div class="panel panel-white no-radius text-center">
									<div class="panel-body">
										<h2 class="StepTitle">Most Popular Appointment Booking Times</h2>
										<div style="max-width: 700px; margin: 50px auto;">
											<canvas id="appointmentTimesChart"></canvas>
										</div>
									</div>
								</div>

								
								<div class="panel panel-white no-radius text-center">
									<div class="panel-body">
										<h2 class="StepTitle">Revenue Generated by Each Doctor Type</h2>
										<div style="max-width: 500px; margin: 50px auto;">
											<canvas id="specializationRevenueChart"></canvas>
										</div>
									</div>
								</div>

							</div>

							
									

		<!-- start: FOOTER -->
		<?php include('include/footer.php'); ?>
		<!-- end: FOOTER -->

		<!-- start: SETTINGS -->
		<?php include('include/setting.php'); ?>
		<>
			<!-- end: SETTINGS -->
	</div>
	<!-- start: MAIN JAVASCRIPTS -->
	<script src="vendor/jquery/jquery.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/modernizr/modernizr.js"></script>
	<script src="vendor/jquery-cookie/jquery.cookie.js"></script>
	<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script src="vendor/switchery/switchery.min.js"></script>
	<!-- end: MAIN JAVASCRIPTS -->
	<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
	<script src="vendor/maskedinput/jquery.maskedinput.min.js"></script>
	<script src="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
	<script src="vendor/autosize/autosize.min.js"></script>
	<script src="vendor/selectFx/classie.js"></script>
	<script src="vendor/selectFx/selectFx.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
	<script src="vendor/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
	<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
	<!-- start: CLIP-TWO JAVASCRIPTS -->
	<script src="assets/js/main.js"></script>
	<!-- start: JavaScript Event Handlers for this page -->
	<script src="assets/js/form-elements.js"></script>
	<script>
		jQuery(document).ready(function() {
			Main.init();
			FormElements.init();
		});
	</script>

	<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<script>
			const monthlyData = <?php echo json_encode($monthlyAppointments); ?>;
			
			// Convert 'year-month' to 'MonthName year'
			
		</script> -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>
		const monthlyData = <?php echo json_encode($monthlyAppointments); ?>;
		const revenueData = <?php echo json_encode($monthlyRevenue); ?>;
		

		// ... [Your existing JavaScript code for the monthly appointments graph]
		const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		const formattedLabels = Object.keys(monthlyData).map(key => {
			const year = key.split('-')[0];
			const monthNum = parseInt(key.split('-')[1]) - 1;
			return monthNames[monthNum] + ' ' + year;
		});

		const ctx = document.getElementById('appointmentsChart').getContext('2d');
		new Chart(ctx, {
			type: 'bar',
			data: {
				labels: formattedLabels,
				datasets: [{
					label: 'Number of Appointments',
					data: Object.values(monthlyData),
					backgroundColor: 'rgba(255, 99, 132, 0.2)',
					borderColor: 'rgba(255, 99, 132, 1)',
					borderWidth: 1,
					hoverBackgroundColor: 'rgba(255, 159, 64, 0.2)',
					hoverBorderColor: 'rgba(255, 159, 64, 1)'
				}]
			},
			options: {
				title: {
					display: true,
					text: 'Monthly Appointments in the Last Year',
					fontSize: 16
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1
						},
						scaleLabel: {
							display: true,
							labelString: 'Number of Appointments'
						}
					},
					x: {
						scaleLabel: {
							display: true,
							labelString: 'Months'
						}
					}
				},
				tooltips: {
					mode: 'index',
					intersect: false,
					backgroundColor: 'rgba(0,0,0,0.7)',
					titleFontColor: '#fff',
					bodyFontColor: '#fff',
					borderColor: 'rgba(255,255,255,0.8)',
					borderWidth: 1
				}
			}
		});


		// JavaScript for the Monthly Revenue Graph
		// const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		const formattedRevenueLabels = Object.keys(revenueData).map(key => {
			const year = key.split('-')[0];
			const monthNum = parseInt(key.split('-')[1]) - 1;
			return monthNames[monthNum] + ' ' + year;
		});

		const revenueCtx = document.getElementById('revenueChart').getContext('2d');
		new Chart(revenueCtx, {
			type: 'line', // Change the graph type to 'line'
			data: {
				labels: formattedRevenueLabels,
				datasets: [{
					label: 'Monthly Revenue',
					data: Object.values(revenueData),
					backgroundColor: 'rgba(75, 192, 192, 0.2)', // Background color for the area under the line
					borderColor: 'rgba(75, 192, 192, 1)', // Color of the line itself
					borderWidth: 2, // Width of the line
					pointBackgroundColor: 'rgba(75, 192, 192, 1)', // Color of the data points
					pointBorderColor: '#fff', // Border color of the data points
					pointBorderWidth: 1, // Border width of the data points
					pointRadius: 4, // Radius of the data points
					fill: true // Whether to fill the area under the line
				}]
			},
			options: {
				title: {
					display: true,
					text: 'Monthly Revenue in the Last Year',
					fontSize: 16
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 200
						},
						scaleLabel: {
							display: true,
							labelString: 'Revenue Amount'
						}
					},
					x: {
						scaleLabel: {
							display: true,
							labelString: 'Months'
						}
					}
				},
				tooltips: {
					mode: 'index',
					intersect: false,
					backgroundColor: 'rgba(0,0,0,0.7)',
					titleFontColor: '#fff',
					bodyFontColor: '#fff',
					borderColor: 'rgba(255,255,255,0.8)',
					borderWidth: 1
				}
			}
		});

		
		const appointmentTimesData = <?php echo json_encode($appointmentTimes); ?>;
		const appointmentTimesLabels = Object.keys(appointmentTimesData).map(hour => {
    return (hour % 12 || 12) + (hour < 12 ? ' AM' : ' PM');
});
const appointmentTimesCtx = document.getElementById('appointmentTimesChart').getContext('2d');
new Chart(appointmentTimesCtx, {
    type: 'bar',
    data: {
        labels: appointmentTimesLabels,  // Use the modified labels here
        datasets: [{
            label: 'Number of Appointments',
            data: Object.values(appointmentTimesData),
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        title: {
            display: true,
            text: 'Most Popular Appointment Booking Times',
            fontSize: 16
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Number of Appointments'
                }
            },
            x: {
                scaleLabel: {
                    display: true,
                    labelString: 'Hour of the Day'
                }
            }
        },
        tooltips: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(0,0,0,0.7)',
            titleFontColor: '#fff',
            bodyFontColor: '#fff',
            borderColor: 'rgba(255,255,255,0.8)',
            borderWidth: 1
        }
    }
});


			const specializationRevenueData = <?php echo json_encode($specializationRevenue); ?>;

			const specializationRevenueCtx = document.getElementById('specializationRevenueChart').getContext('2d');
			new Chart(specializationRevenueCtx, {
				type: 'pie',
				data: {
					labels: Object.keys(specializationRevenueData),
					datasets: [{
						data: Object.values(specializationRevenueData),
						backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', '#FFCD56', '#C9CBCF', '#EC932F', '#2E7D32', '#283593'],
						borderColor: '#fff'
					}]
				},
				options: {
					title: {
						display: true,
						text: 'Revenue Generated by Each Doctor Type',
						fontSize: 16
					}
				}
			});
	</script>

	<!-- end: JavaScript Event Handlers for this page -->
	<!-- end: CLIP-TWO JAVASCRIPTS -->
</body>

</html>