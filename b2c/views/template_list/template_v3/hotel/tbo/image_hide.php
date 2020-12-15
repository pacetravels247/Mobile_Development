<?php
print_r($HotelPicture);exit;
header("Content-type: image/gif");
echo  file_get_contents("https://cdn.grnconnect.com/hotels/images/d4/62/d4627194eab77ccef228ceed60cb8be6ae1805eb.jpg");

?>
