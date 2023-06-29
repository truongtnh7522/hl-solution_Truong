<?php
$numbers = [1, 2, 3, 4, 5];


$totalSum = array_sum($numbers);


$minSum = $totalSum - max($numbers);
$maxSum = $totalSum - min($numbers);

echo  $minSum ." ". $maxSum."<br>"."<br>";

echo "Our initial numbers are " . implode(', ', $numbers) . ".<br>";
echo "We can calculate the following sums using four of the five integers:<br>";
$counter = 1;
foreach ($numbers as $number) {
  $sum = $totalSum - $number;
  echo $counter . ": If we sum everything except " . $number . ", our sum is: " . $sum . "<br>";
  $counter++;
}


?>
