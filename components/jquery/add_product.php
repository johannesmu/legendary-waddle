<?php
include("includes/session.php");
include("includes/dbconnection.php");

?>
<?doctype html>
<html>
<?php include("head.php");?>
<body>
  <?php include("navigation.php");?>
  <div class="container">
    <div class="row">
      <div role="tabpanel" class="tab-pane" id="addproduct">
        <h3>Add a new item</h3>
          <form id="new-item" action="dashboard.php" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Item Name</label>
                  <input id="name" name="name" class="form-control" type="text" placeholder="Item Name">
                </div>
                <div class="form-group">
                  <label for="description">Item Description</label>
                  <textarea id="description" name="description" class="form-control" placeholder="Item Description"></textarea>
                </div>
                <div class="form-group">
                  <label for="quantity">Quantity in stock</label>
                  <input id="quantity" name="quantity" class="form-control number" type="number" value="10">
                </div>
                <div class="form-group">
                  <label for="category">Item Category</label>
                  <select id="category" class="form-control" name="category">
                    <?php
                    foreach($categories as $cat){
                      $id = $cat["category_id"];
                      $name = $cat["name"];
                      echo "<option value=\"$id\">$name</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="image">
                  Product Image
                  </label>
                  <input id="image" name="image" class="form-control" type="file">
                </div>
              </div>
              
            </div>
            <button class="btn btn-default" type="submit" role="submit">Create Item</button>
          </form>
      </div>
    </div>
  </div>
  <?php include("includes/scripts.php");?>
</body>
</html>