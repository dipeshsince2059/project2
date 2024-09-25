<?php 
include('../connect.php');

if(!isset($_SESSION['uid'])){
  echo "<script> window.location.href='../login.php';  </script>";
}

$editMode = false;
$editData = [];

// Check if an edit ID is provided
if (isset($_GET['editid'])) {
    $editid = $_GET['editid'];
    $sql = "SELECT * FROM `movies` WHERE movieid = '$editid'";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {
        $editData = mysqli_fetch_array($res);
        $editMode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies</title>
</head>
<body>

<?php include('header.php') ?>

<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <form action="movies.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="movieid" value="<?= $editMode ? $editData['movieid'] : '' ?>">

                <div class="form-group mb-4">
                    <select name="catid" class="form-control">
                        <option value="">Select Category</option>
                        <?php
                        $sql = "SELECT * FROM `categories`";
                        $res = mysqli_query($con, $sql);
                        if (mysqli_num_rows($res) > 0) {
                            while ($data = mysqli_fetch_array($res)) {
                                $selected = ($editMode && $data['catid'] == $editData['catid']) ? 'selected' : '';
                                echo "<option value=\"{$data['catid']}\" $selected>{$data['catname']}</option>";
                            }
                        } else {
                            echo '<option value="">No Category found</option>';
                        }
                        ?> 
                    </select>
                </div>

                <div class="form-group mb-4">
                    <input type="text" class="form-control" name="title" value="<?= $editMode ? $editData['title'] : '' ?>" placeholder="Enter title">
                </div>

                <div class="form-group mb-4">
                    <input type="text" class="form-control" name="description" value="<?= $editMode ? $editData['description'] : '' ?>" placeholder="Enter description">
                </div>

                <div class="form-group mb-4">
                    <input type="date" class="form-control" name="releasedate" value="<?= $editMode ? $editData['releasedate'] : '' ?>">
                </div>

                <div class="form-group mb-4">
                    Poster:
                    <input type="file" class="form-control" name="image">
                </div>

                <div class="form-group mb-4">
                    Trailer:
                    <input type="file" class="form-control" name="trailer">
                </div>

                <div class="form-group mb-4">
                    Video:
                    <input type="file" class="form-control" name="movie">
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="<?= $editMode ? 'Update' : 'Add' ?>" name="<?= $editMode ? 'update' : 'add' ?>">
                </div>
            </form>
        </div>

        <div class="col-lg-6">
            <table class="table">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Poster</th>
                    <th>Action</th>
                </tr>
                
                <?php
                $sql = "SELECT movies.*, categories.catname FROM movies INNER JOIN categories ON categories.catid = movies.catid";
                $res = mysqli_query($con, $sql);
                if (mysqli_num_rows($res) > 0) {
                    while ($data = mysqli_fetch_array($res)) {
                        echo "<tr>
                                <td>{$data['movieid']}</td>
                                <td>{$data['title']}</td>
                                <td>{$data['catname']}</td>
                                <td><img src=\"uploads/{$data['image']}\" height=\"50\" width=\"50\" alt=\"\"></td>
                                <td>
                                    <a href=\"movies.php?editid={$data['movieid']}\" class=\"btn btn-primary\">Edit</a>
                                    <a href=\"movies.php?deleteid={$data['movieid']}\" class=\"btn btn-danger\">Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo 'No movies found';
                }
                ?>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php') ?>

</body>
</html>

<?php
if (isset($_POST['add'])) {
    // Add movie logic
    $catid = $_POST['catid'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $releasedate = $_POST['releasedate'];
    
    $image = $_FILES['image']['name'] ?? '';
    $tmp_image = $_FILES['image']['tmp_name'] ?? '';
    
    $trailer = $_FILES['trailer']['name'] ?? '';
    $tmp_trailer = $_FILES['trailer']['tmp_name'] ?? '';
    
    $movie = $_FILES['movie']['name'] ?? '';
    $tmp_movie = $_FILES['movie']['tmp_name'] ?? '';

    // Move uploaded files
    if ($image) move_uploaded_file($tmp_image, "uploads/$image");
    if ($trailer) move_uploaded_file($tmp_trailer, "uploads/$trailer");
    if ($movie) move_uploaded_file($tmp_movie, "uploads/$movie");

    $sql = "INSERT INTO `movies`(`title`, `description`, `releasedate`, `image`, `trailer`, `movie`, `catid`) 
            VALUES ('$title','$description','$releasedate','$image','$trailer','$movie','$catid')";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Movie added')</script>";
        echo "<script>window.location.href='movies.php'</script>";
    } else {
        echo "<script>alert('Movie not added')</script>";
    }
}

if (isset($_POST['update'])) {
    // Update movie logic
    $movieid = $_POST['movieid'];
    $catid = $_POST['catid'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $releasedate = $_POST['releasedate'];
    
    $image = $_FILES['image']['name'] ?? '';
    $tmp_image = $_FILES['image']['tmp_name'] ?? '';
    
    $trailer = $_FILES['trailer']['name'] ?? '';
    $tmp_trailer = $_FILES['trailer']['tmp_name'] ?? '';
    
    $movie = $_FILES['movie']['name'] ?? '';
    $tmp_movie = $_FILES['movie']['tmp_name'] ?? '';

    // Prepare the SQL query
    $sql = "UPDATE `movies` SET 
            `title` = '$title', 
            `description` = '$description', 
            `releasedate` = '$releasedate', 
            `catid` = '$catid'";

    // Update file paths if new files are uploaded
    if ($image) {
        move_uploaded_file($tmp_image, "uploads/$image");
        $sql .= ", `image` = '$image'";
    }
    if ($trailer) {
        move_uploaded_file($tmp_trailer, "uploads/$trailer");
        $sql .= ", `trailer` = '$trailer'";
    }
    if ($movie) {
        move_uploaded_file($tmp_movie, "uploads/$movie");
        $sql .= ", `movie` = '$movie'";
    }

    $sql .= " WHERE `movieid` = '$movieid'";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Movie updated')</script>";
        echo "<script>window.location.href='movies.php'</script>";
    } else {
        echo "<script>alert('Movie not updated')</script>";
    }
}

if (isset($_GET['deleteid'])) {
    $deleteid = $_GET['deleteid'];
    $sql = "DELETE FROM `movies` WHERE movieid = '$deleteid'";
    
    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Movie deleted')</script>";
        echo "<script>window.location.href='movies.php'</script>";
    } else {
        echo "<script>alert('Movie not deleted')</script>";
    }
}
?>
