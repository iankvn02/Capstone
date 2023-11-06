<!-- Applicants Documents -->
<div class="modal fade" id="details2<?php echo $row['user_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Applicants Documents</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <table class="table table-bordered table-striped">
                    <thead>
                                                                        <tr>
                                                                            <th>Applicant Number</th>
                                                                            <th>Types of Application</th>
                                                                            <th>Name</th>
                                                                            <th>Email</th>
                                                                            
                                                                        </tr>
                                                                    </thead>
                        <tbody>
                                                                        <?php
                                                                        include("conn.php");
                                                                        // Create a connection to the database
                                                                        $conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

                                                                        // Query to select the row based on the provided 'id'
                                                                        $sql = "select * from applicant_documents where id='" . $row['id'] . "'";
                                                                        $query = $conn->query($sql);
                                                                        $id = $row['id'];
                                                                        while ($row = $query->fetch_array()) {
                                                                        ?>
                                                                            <tr>
                                                                                <td><?php echo $row['id']; ?></td>
                                                                                <td><a href="<?php echo $row['birth_certificate']?>">View PDF</a></td>
                                                                                <td><a href="<?php echo $row['birth_certificate']?>">View PDF</a></td>
                                                                                <td><a href="<?php echo $row['birth_certificate']?>">View PDF</a></td>
                                                                              
                                                                            </tr>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Close</button>
                                                        </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
