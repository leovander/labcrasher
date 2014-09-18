
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Lab Crasher</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link href="styles.css" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="available.js"></script>
  </head>

  <body>

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="/labcrasher/test.html">Home</a></li>
          <li><a href="#">About</a></li>
        </ul>
        <h3 class="text-muted">Lab Crasher</h3>
      </div>

      <div class="jumbotron">
        <p>Choose</p>
        <p>Choose College Below</p>
        <form>
			<select class="form-control" id="colleges">
				<option value="CECS">CECS</option>
				<option value="CEM">CEM</option>
				<option value="CHzE">CHzE</option>
				<option value="CzE">CzE</option>
				<option value="ENGR">ENGR</option>
				<option value="EzE">EzE</option>
				<option value="EzT">EzT</option>
				<option value="MAE">MAE</option>
			</select>
		</form>
      </div>
      
      <div class="row">
      	<table class="table">
		  <tbody id="available">
		  	<tr>
			  	<th>Room</th>
			  	<th>Time Remaining</th>
		  	</tr>
		  	<tr>
		  		<td>VEC-418</td>
		  		<td>
		  			<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
    60%
  </div>
</div>
		  		</td>
		  	</tr>
		  </tbody>
		</table>
      </div>

      <div class="footer">
        <p>&copy; Company 2014</p>
      </div>

    </div> <!-- /container -->

  </body>
</html>
