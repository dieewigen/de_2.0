<?php
$schiffsname['1']['0']='Hornet';
$schiffsname['1']['1']='Guillotine';
$schiffsname['1']['2']='Jackal';
$schiffsname['1']['3']='Marauder';
$schiffsname['1']['4']='Cerberus';
$schiffsname['1']['5']='Hydra';
$schiffsname['1']['6']='Transmitter';

$schiffsname['2']['0']='Caesar';
$schiffsname['2']['1']='Paladin';
$schiffsname['2']['2']='Executor';
$schiffsname['2']['3']='Emperor';
$schiffsname['2']['4']='Excalibur';
$schiffsname['2']['5']='Colossus';
$schiffsname['2']['6']='Merlin';

$schiffsname['3']['0']='Spider';
$schiffsname['3']['1']='Arctic Spider';
$schiffsname['3']['2']='Werespider';
$schiffsname['3']['3']='Tarantula';
$schiffsname['3']['4']='Black Widow';
$schiffsname['3']['5']='Gigantula';
$schiffsname['3']['6']='Webcatcher';

$schiffsname['4']['0']='Wasp';
$schiffsname['4']['1']='Fire Scorpion';
$schiffsname['4']['2']='Ghost Strider';
$schiffsname['4']['3']='Scarab';
$schiffsname['4']['4']='Mantis';
$schiffsname['4']['5']='Disgust Breeder';
$schiffsname['4']['6']='Scavenger';

$schiffsname['5']['0']='Hornets';
$schiffsname['5']['1']='Guillotines';
$schiffsname['5']['2']='Jackals';
$schiffsname['5']['3']='Marauder';
$schiffsname['5']['4']='Cerberus';
$schiffsname['5']['5']='Hydras';
$schiffsname['5']['6']='Transmitters';

$schiffsname['6']['0']='Caesars';
$schiffsname['6']['1']='Paladines';
$schiffsname['6']['2']='Executors';
$schiffsname['6']['3']='Emperors';
$schiffsname['6']['4']='Excaliburs';
$schiffsname['6']['5']='Colossus';
$schiffsname['6']['6']='Merlins';

$schiffsname['7']['0']='Spiders';
$schiffsname['7']['1']='Arctic Spiders';
$schiffsname['7']['2']='Werespiders';
$schiffsname['7']['3']='Tarantulas';
$schiffsname['7']['4']='Black Widows';
$schiffsname['7']['5']='Gigantulas';
$schiffsname['7']['6']='Webcatcher';

$schiffsname['8']['0']='Wasps';
$schiffsname['8']['1']='Fire Scorpions';
$schiffsname['8']['2']='Ghost Striders';
$schiffsname['8']['3']='Scarabs';
$schiffsname['8']['4']='Mantis';
$schiffsname['8']['5']='Disgust Breeder';
$schiffsname['8']['6']='Scavengers';

$turm['1']['0']='Interceptor Garrison';
$turm['1']['1']='Missile Tower';
$turm['1']['2']='Laser Tower';
$turm['1']['3']='Auto Cannon Tower';
$turm['1']['4']='Plasma Tower';

$turm['2']['0']='Crusher Garrison';
$turm['2']['1']='Ballistic Tower';
$turm['2']['2']='Laser Lance Tower';
$turm['2']['3']='Bolt Cannon Tower';
$turm['2']['4']='Plasma Lance Tower';

$turm['3']['0']='Defender Swarm';
$turm['3']['1']='Spores Gland';
$turm['3']['2']='Light Gland';
$turm['3']['3']='Matter Gland';
$turm['3']['4']='Plasma Gland';

$turm['4']['0']='Larvae Hive';
$turm['4']['1']='Spittle Battery';
$turm['4']['2']='Ground Stinger';
$turm['4']['3']='Venom Stinger Battery';
$turm['4']['4']='Fire Stinger Battery';

$turm['5']['0']='Interceptor Garrisons';
$turm['5']['1']='Missile Towers';
$turm['5']['2']='Laser Towers';
$turm['5']['3']='Auto Cannon Towers';
$turm['5']['4']='Plasma Towers';

$turm['6']['0']='Crusher Garrisons';
$turm['6']['1']='Ballistic Towers';
$turm['6']['2']='Laser Lance Towers';
$turm['6']['3']='Bolt Cannon Towers';
$turm['6']['4']='Plasma Lance Towers';

$turm['7']['0']='Defender Swarms';
$turm['7']['1']='Spores Glands';
$turm['7']['2']='Light Glands';
$turm['7']['3']='Matter Glands';
$turm['7']['4']='Plasma Glands';

$turm['8']['0']='Larvae Hives';
$turm['8']['1']='Spittle Batteries';
$turm['8']['2']='Ground Stingers';
$turm['8']['3']='Venom Stinger Batteries';
$turm['8']['4']='Fire Stinger Batteries';

$ressourcenart['0']='Multiplex';
$ressourcenart['1']='Dyharra';
$ressourcenart['2']='Iradium';
$ressourcenart['3']='Eternium';



$kolliemsg['0']=' A big company invests in your technology and contributes '.number_format($zufallskollies, 0,','.').' collectors.';
$kolliemsg['1']=' A private research center develops an new control system for collectors and sends you for testing purpose '.number_format($zufallskollies, 0,','.').' collector prototypes for free.';
$kolliemsg['2']=' A private university developed a better collector cell and sends you '.number_format($zufallskollies, 0,','.').' prototypes to verify their testing results.';
$kolliemsg['3']=' A training escort finds between the wreckage of old ships '.number_format($zufallskollies, 0,','.').' lost collectors.';
$kolliemsg['4']=' During a heavy hyperspace storm '.number_format($zufallskollies, 0,','.').' collectors appear in the orbit of your planet.';
$kolliemsg['5']=' A new self replicating biological collector factory could increase the amount of collectors by '.number_format($zufallskollies, 0,','.').' without any costs.';

$schiffmsg['0']=' You win '.number_format($anzahl, 0,','.').' '.$schiffsname['$zufallsrasse']['$schiffsart'].'.';

$deffmsg['0']=' You win '.number_format($zufallsturm, 0,','.').' '.$turm['$zufallsrasse']['$turmart'].' as first price in a singing contest.';
$deffmsg['1']=' With improvements in their nanobot technologie your engineer could repair '.number_format($zufallsturm, 0,','.').' broken '.$turm['$zufallsrasse']['$turmart'].'.';
$deffmsg['2']=' An unknown person sends you '.number_format($zufallsturm, 0,','.').' '.$turm['$zufallsrasse']['$turmart'].' as a gift.';
//$deffmsg['3']=' Polizeieinheiten beschlagnahmen '.number_format($zufallsturm, 0,','.').' funktionsfähige '.$turm['$zufallsrasse']['$turmart'].' der Rüstungsmafia.';
//$deffmsg['4']=' Ein namenhafter Konzern hat ihnen einige unzulässige Vertragsbedingungen untergejubelt und wurde von einem Gericht zur kostenfreien Lieferung von '.number_format($zufallsturm, 0,','.').' '.$turm['$zufallsrasse']['$turmart'].' verurteilt.';
//$deffmsg['5']=' Bei einer Razzia einer illegalen Kriegsgerätemesse können Polizeieinheiten '.number_format($zufallsturm, 0,','.').' '.$turm['$zufallsrasse']['$turmart'].' beschlagnahmen.';
//$deffmsg['6']=' Eine spontane Mutation eines Nanobotstamms repliziert '.number_format($zufallsturm, 0,','.').' '.$turm['$zufallsrasse']['$turmart'].' bevor er sich wegen unkontrollierter Verbreitung selbst vernichtet.';

$erfahrungsmsg['0']=' Your pilots fly a maneuver. The commanding officer wins '.number_format($zufallerfahrung, 0,','.').' experience points.';
$erfahrungsmsg['1']=' In a maneuver the pilot has an accident. At least he learned something and gets ('.number_format($zufallerfahrung, 0,','.').' experience points).';

$agentenmsg['0']=' Some agents of the enemy change the lines. After an inspection of their integrity '.number_format($zufallsagenten, 0,','.').' agents can begin their work.';
$agentenmsg['1']=' After an image campaign many new employees for the secret service are recruited. '.number_format($zufallsagenten, 0,','.').' new agents can begin their work.';
//$agentenmsg['2']=' Ihr Nachrichtendienst führte eine umfangreiche Rekrutierungsaktion bester Köpfe durch. Von allen neu zugewiesenen Identitäten wurden '.number_format($zufallsagenten, 0,','.').' angenommen, um die übrigen wurde sich gekümmert. Die Familien erhielten die übliche Zuwendung.';
//$agentenmsg['3']=' Durch eine Zusammenlegung zweier Abteilungen ihres Geheimdienstes konnten Stellen gespart werden. Es konnten '.number_format($zufallsagenten, 0,','.').' Agenten für den Au&szlig;endienst mobilisiert werden, die übrigen erhielten die nötige Aufmerksamkeit.';
//$agentenmsg['4']=' Durch neue, psychische Trainigsmethoden konnten '.number_format($zufallsagenten, 0,','.').' Agenten die für den Au&szlig;endienst nötige Sicherheitsstufe erlangen.';
//$agentenmsg['5']=' Einige in Haft befindliche Agenten konnten durch chemische Umerziehungsma&szlig;nahmen wieder auf Linie gebracht werden. '.number_format($zufallsagenten, 0,','.').' Agenten überstanden diese.';
//$agentenmsg['6']=' Eine Befreiungsaktion brachte einige verloren geglaubte Agenten auf. '.number_format($zufallsagenten, 0,','.').' von ihnen überstanden alles ohne bleibende Schäden, die anderen wurden kostengünstig in den Ruhestand geschickt.';
//$agentenmsg['7']=' Durch das Klonen ihrer besten Agenten konnte das nachrichtendienstliche Personal um '.number_format($zufallsagenten, 0,','.').' Agenten aufgestockt werden.';

$sondenmsg['0']=' In a meteor storm your fleets find. '.number_format($zufallssonden, 0,','.').' espionage probes.';
$sondenmsg['1']=' As bonus for a big research order your secret service gets '.number_format($zufallssonden, 0,','.').' espionage probes.';
//$sondenmsg['2']=' Beim testen einer selbstreplizierenden Sonde enstanden auf Anhieb '.number_format($zufallssonden, 0,','.').' funktionsfähige Sonden, alle weiteren waren allerdings fehlerhaft, weshalb das Projekt erstmal auf Eis gelegt wurde.';

$ressmsg['0']=' Your fleets discover the wreckage of a big space battle that brings '.number_format($anzahl, 0,','.').' '.$ressourcenart['$ressart'].' from recycling.';
$ressmsg['1']=' An error of an engineer makes some of your converters run on 120% for a short period. The result is '.number_format($anzahl, 0,','.').' '.$ressourcenart['$ressart'].' additional output. Fortunately the converter did not take any damage.';
//$ressmsg['2']=' Bergarbeiter stie&szlig;en auf einem Asteroiden auf besonders reiche und reine '.$ressourcenart['$ressart'].'vorkommen. '.number_format($anzahl, 0,','.').' Einheiten davon stehen sofort zur Verfügung.';

?>
