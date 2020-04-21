<?php
$title = "Chapter Election Portal | Occoneechee Lodge - Order of the Arrow, BSA";
$userrole = "Standard User"; // Allow only logged in users
include "../login/misc/pagehead.php";

include '../unitelections-info.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>


<!DOCTYPE html>
<html>

<head>
    <meta http-equiv=X-UA-Compatible content="IE=Edge,chrome=1" />
    <meta name=viewport content="width=device-width,initial-scale=1.0,maximum-scale=1.0" />


    <title>Dashboard | Unit Elections Administration | Occoneechee Lodge - Order of the Arrow, BSA</title>
	
	<link rel="stylesheet" href="../libraries/fontawesome-free-5.12.0/css/all.min.css">


</head>

<body id="dashboard">
	<?php require '../login/misc/pullnav.php'; ?>
  <div class="wrapper">

    <main class="container-fluid col-xl-11">
      <?php
      if ($_GET['status'] == 1) { ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <div class="alert alert-success" role="alert">
            <strong>Saved!</strong> Your data has been saved! Thanks!
            <button type="button" class="close" data-dismiss="alert"><i class="fas fa-times"></i></button>
        </div>
    <?php } ?>
        <section class="row">
            <div class="col-12">
                <h2>Chapter's Unit Elections Dashboard</h2>
            </div>
        </section>

        <?php
          $getChaptersQuery = $conn->prepare("SELECT DISTINCT chapter FROM unitElections ORDER BY chapter ASC");
          $getChaptersQuery->execute();
          $getChaptersQ = $getChaptersQuery->get_result();
          if ($getChaptersQ->num_rows > 0) {
            while ($getChapters = $getChaptersQ->fetch_assoc()) {
              $getUnitElectionsQuery = $conn->prepare("SELECT * from unitElections where chapter = ? ORDER BY dateOfElection ASC");
              $getUnitElectionsQuery->bind_param("s", $getChapters['chapter']);
              $getUnitElectionsQuery->execute();
              $getUnitElectionsQ = $getUnitElectionsQuery->get_result();
              if ($getUnitElectionsQ->num_rows > 0) {
                //print election info
                ?>
                <div class="card mb-3">
                  <div class="card-body">
                    <h3 class="card-title"><?php echo $getChapters['chapter']; ?></h3>
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Unit</th>
                            <th scope="col">Date of Election</th>
                            <th scope="col">Access Key for Unit Leader</th>
							<th scope="col">Eligible Scouts</th>
                            <th scope="col">Voting Link</th>
                            <th scope="col">Edit</th>
							<th scope="col"># of Votes</th>
							<th scope="col">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($getUnitElections = $getUnitElectionsQ->fetch_assoc()){
                            ?><tr>
                              <td><?php echo $getUnitElections['unitCommunity'] . " " . $getUnitElections['unitNumber']; ?></td>
                              <td><?php echo date("m-d-Y", strtotime($getUnitElections['dateOfElection'])); ?></td>
							  <td><input id="key" type="text" value="<?php echo $getUnitElections['accessKey']; ?>" disabled><button class="btn btn-primary" id="btn" data-clipboard-text="<?php echo $getUnitElections['accessKey']; ?>">Copy</button>
						   	  </td>
                              <td><a href="eligible-scouts.php?accessKey=<?php echo $getUnitElections['accessKey']; ?>" class="btn btn-primary" role="button">Edit</a></td>
                              <td><input id="foo" type="text" value="https://elections.lodge104.net/submit.php?accessKey=<?php echo $getUnitElections['accessKey']; ?>" disabled><button class="btn btn-primary" id="btn" data-clipboard-text="https://elections.lodge104.net/submit.php?accessKey=<?php echo $getUnitElections['accessKey']; ?>">Copy</button>
                              </td>
                              <td><a href="edit-unit-election.php?accessKey=<?php echo $getUnitElections['accessKey']; ?>" class="btn btn-primary" role="button">Edit</a></td>
							  <?php
                              $submissionsQuery = $conn->prepare("SELECT COUNT(*) AS unitTotal FROM submissions WHERE unitId=?");
                              $submissionsQuery->bind_param("s", $getUnitElections['id']);
                              $submissionsQuery->execute();
                              $submissionsQ = $submissionsQuery->get_result();
                              if ($submissionsQ->num_rows > 0) {
                                $submissions = $submissionsQ->fetch_assoc();
                                ?><td><?php echo $submissions['unitTotal']; ?> out of <?php echo $getUnitElections['numRegisteredYouth']; ?> Scouts</td>
                              <?php }
                              $submissionsQuery->close();
                              ?>
							  <td>
								  <?php
								  if (($getUnitElections['exported'] == 'Yes')) { ?>
                                  <span class="badge badge-success">Imported</span>
                                <?php } elseif (($getUnitElections['open'] == 'Yes')) { ?>
                                  <span class="badge badge-warning">Voting Open</span>
                                <?php } else { ?>
                                  <span class="badge badge-danger">Voting Not Open</span>
                                <?php } ?>
							  </td>	  
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <?php
              } else {
                ?>
                <div class="alert alert-danger" role="alert">
                  There are no elections in the database.
                </div>
                <?php
              }
            }
          } else {
            ?>
            <div class="alert alert-danger" role="alert">
              There are no elections in the database.
            </div>
            <?php
          }
        ?>

    </main>
  </div>
    <?php include "../footer.php"; ?>

    <script src="../libraries/jquery-3.4.1.min.js"></script>
    <script src="../libraries/popper-1.16.0.min.js"></script>
    <script src="../libraries/bootstrap-4.4.1/js/bootstrap.min.js"></script>
	<script src="../dist/clipboard.min.js"></script>

    								<script>
    									var clipboard = new ClipboardJS('.btn');

    									clipboard.on('success', function(e) {
												console.log(e);
										});

										clipboard.on('error', function(e) {
											console.log(e);
										});
								    </script>

</body>

</html>
