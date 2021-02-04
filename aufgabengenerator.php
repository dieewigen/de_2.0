<?
$operator = rand(0,3);

switch ($operator) {
case 0:
$zahleins = rand(1,10);
$zahlzwei = rand(1,10);
$erg=$zahleins+$zahlzwei;
break;

case 1:
$temp=0;
while($temp!=1)
{
$zahleins = rand(1,20);
$zahlzwei = rand(1,19);
if(($zahleins!=$zahlzwei)&&($zahleins>$zahlzwei))$temp++;
}
$erg=$zahleins-$zahlzwei;
break;

case 2:
$zahleins = rand(1,10);
$zahlzwei = rand(1,10);
$erg=$zahleins*$zahlzwei;
break;

case 3:
$temp=0;
while($temp<=1)
{
$zahleins = rand(1,50);
$zahlzwei = rand(2,50);
if((($zahleins%$zahlzwei)==0)&&($zahleins!=$zahlzwei))$temp=2;
}
$erg=$zahleins/$zahlzwei;
break;
}

$_SESSION['zahleins']=$zahleins;
$_SESSION['zahlzwei']=$zahlzwei;
$_SESSION['operator']=$operator;
$_SESSION['ergebnis']=$erg;
?>