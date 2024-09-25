<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title> 
</head> 
<body> 

<?php include('connect.php'); ?>
<?php include('header.php'); ?>

<section id="team" class="team section-bg">
    <div class="container aos-init aos-animate" data-aos="fade-up">

        <div class="section-title">
            <h2>Latest Movies</h2>
            <h3>NOW <span>PLAYING</span></h3>
        </div>

        <form action="index.php" method="post">
            <div class="row">
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="form-group">
                        <input type="text" class="form-control" name="movie_search" placeholder="Search Movie Name">
                    </div>    
                </div>
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="form-group">
                        <select name="catid" class="form-control">
                            <option value="">Select Category</option>
                            <?php
                            $sql = "SELECT * FROM `categories`";
                            $res = mysqli_query($con, $sql);
                            if (mysqli_num_rows($res) > 0) {
                                while ($data = mysqli_fetch_array($res)) {
                                    echo "<option value=\"{$data['catid']}\"> {$data['catname']} </option>";   
                                }
                            } else {
                                echo "<option value=\"\">No Category found</option>";  
                            }  
                            ?> 
                        </select>
                    </div>
                </div>
                <div class="col-lg-1 col-md-6 d-flex">
                    <div class="form-group">
                        <input type="submit" name="btnSearch" value="Search" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </form>

        <div class="row mt-5">
            <?php
            if (isset($_POST['btnSearch'])) {
                $movie_search = $_POST['movie_search'];
                $catid = $_POST['catid'];

                $sql = "SELECT movies.*, categories.catname
                        FROM movies
                        INNER JOIN categories ON categories.catid = movies.catid
                        WHERE movies.title LIKE '%$movie_search%' AND movies.catid = '$catid'";
                $res = mysqli_query($con, $sql);
                
                if (mysqli_num_rows($res) > 0) {
                    while ($data = mysqli_fetch_array($res)) {
            ?>
                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                            <div class="member">
                                <div class="member-img">
                                    <img src="admin/uploads/<?= $data['image'] ?>" style="height:250px !important; width:250px !important;" alt="">
                                    <div class="social">
                                        <a href="admin/uploads/<?= $data['trailer'] ?>" target="_blank" class="btn btn-primary" style="width:150px;">Watch Trailer</a>
                                        <a href="booking.php?id=<?= $data['theaterid'] ?>" target="_blank" class="btn btn-primary" style="width:150px;">Book Now</a>
                                    </div>
                                </div>
                                <div class="member-info">
                                    <h4><?= $data['title'] ?></h4>
                                    <span><?= $data['catname'] ?></span>
                                </div>
                            </div>
                        </div>
            <?php
                    }
                } else {
                    echo "<p>No movies found.</p>";
                }
            } else {
                // Display all movies if no search is performed
                $sql = "SELECT movies.*, categories.catname
                        FROM movies
                        INNER JOIN categories ON categories.catid = movies.catid
                        ORDER BY movies.movieid DESC";
                $res = mysqli_query($con, $sql);
                
                if (mysqli_num_rows($res) > 0) {
                    while ($data = mysqli_fetch_array($res)) {
            ?>
                      <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
    <div class="member">
        <div class="member-img">
            <img src="admin/uploads/<?= $data['image'] ?>" style="height:250px !important; width:250px !important;" alt="">
            <div class="social">
                <a href="admin/uploads/<?= $data['trailer'] ?>" target="_blank" class="btn btn-primary" style="width:150px;">Watch Trailer</a>
                <a href="booking.php?movieid=<?= $data['movieid'] ?>" class="btn btn-secondary" style="width:150px;">Book Now</a>
            </div>
        </div>
        <div class="member-info">
            <h4><?= $data['title'] ?></h4>
            <span><?= $data['catname'] ?></span>
        </div>
    </div>
</div>

            <?php
                    }
                } else {
                    echo "<p>No movies found.</p>";
                }
            }
            ?>
        </div>
    </div> 
</section> 

<?php include('footer.php'); ?> 

</body> 
</html>
