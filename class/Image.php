<?php
require_once 'Database.php';

class Image
{
    private $conn;

    public function __construct()
    {
        $database   = new Database();
        $db         = $database->dbConnection();
        $this->conn = $db;
    }

    public function query($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }

    public function upload($image, $image_tmp, $image_ext)
    {
        try {
            $this->conn->beginTransaction();

            // rename image to time() to avoid duplicate
            $image = $image . '_' . time() . '.' . $image_ext;

            // insert new image to db
            $stmt = $this->conn->prepare("INSERT images (image_name) VALUES (?)");
            $stmt->execute([$image]);

            // set upload directory
            /*
                your upload directory will be based from your form, that's why even though your Image.php is inside class folder, you don't have to go outside class folder by setting "../upload/".
            */
            $upload_dir = 'upload/';
            // upload image
            move_uploaded_file($image_tmp, $upload_dir . $image);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollback();
            echo $e->getMessage();
        }
    }

    public function update($image, $image_tmp, $image_ext, $id)
    {
        try {
            $this->conn->beginTransaction();

            // rename image to time() to avoid duplicate
            $image = $image . '_' . time() . '.' . $image_ext;

            $upload_dir = 'upload/';

            // get old image
            $oldImage = $this->conn->prepare("SELECT image_name FROM images WHERE id=?");
            $oldImage->execute([$id]);
            $rowOldImage = $oldImage->fetch();

            // delete old image
            unlink($upload_dir . $rowOldImage['image_name']);

            // update image name in database
            $stmt = $this->conn->prepare("UPDATE images SET image_name=? WHERE id=?");
            $stmt->execute([$image, $id]);

            // upload new image
            move_uploaded_file($image_tmp, $upload_dir . $image);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollback();
            echo $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();

            // get image name
            $stmt = $this->conn->prepare("SELECT image_name FROM images WHERE id=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();

            $upload_dir = 'upload/';
            // delete image
            unlink($upload_dir . $row['image_name']);

            // delete image in database
            $delete = $this->conn->prepare("DELETE FROM images WHERE id=?");
            $delete->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollback();
            echo $e->getMessage();
        }
    }
}
