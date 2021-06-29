<?php
// Para que não haja tempo maximo de execução
set_time_limit(0);
// Function que será chamada após o loop infinito com slepp
function FindNewBlock() {
// Aqui pegamos a dificuldade e setamos o time são paulo américa
date_default_timezone_set('America/Sao_Paulo');
$response = file_get_contents("https://ergo.herominers.com/api/stats");
$response = json_decode($response);

// Aqui vamos consultar a contribuição de cada um e dividir pela dificuldade anterior, já salvando num array
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://ergo.herominers.com/api/stats_address?address=9ghYV3v4sKWdRFgJ8hTgh5V61s5D1iNhbzAEW4KuUT6USWhRYPa&recentBlocksAmount=20&longpoll=false');
$headers = array();
$headers[] = 'cookie: SLG_GWPT_Show_Hide_tmp=1; SLG_wptGlobTipTmp=1; mining_address=9ghYV3v4sKWdRFgJ8hTgh5V61s5D1iNhbzAEW4KuUT6USWhRYPa';
$headers[] = 'referer: https://ergo.herominers.com/';
$headers[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36 OPR/75.0.3969.285';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$result = json_decode($result);
$workers = $result->workers;
$currentMemberEffort = [];
$time = date('d/m/Y H:i:s');
$dateTime = date('d/m/Y');
$countBlock = (int) $result->stats->blocksFound;
$allMembersHash = $result->stats->soloRoundHashes;
$diff = $response->network->difficulty;
$luckyCurrent = $allMembersHash / $diff * 100;
$formatted_lucky = number_format($luckyCurrent, 0, ',', ' ');
$luckyText = "A sorte do bloco é de <b>$formatted_lucky%</b> ";
$sharesSum = 0;
$findBlock = 0;
$blockCountOffline = file_get_contents("bloco.txt");
$msgText = [];
$inverseMsg = [];
$excel = [];
foreach ($workers as $worker) {
  if ($worker->hashrate_6h > 0){
  $sharesSum += ($worker->hashrate_6h);
  }
}

foreach ($workers as $worker) {
    if ($worker->hashrate_6h > 0){
  $name = $worker->name;
  $porcent = ($worker->hashrate_6h * 100) /$sharesSum;
  $formatted_value = number_format($porcent, 1, ',', ' ');
  $currentMemberEffort[] =  [
    'name' =>  $name,
    'porcent' =>  $formatted_value . "%"
  ];
  }
}
sort($currentMemberEffort);

if ($countBlock > $blockCountOffline ) {
  $newBlock =  "<b>Acabamos de achar um bloco $time</b>";
  $findBlock = 1;
  $var=fopen("bloco.txt","w");
  fwrite($var, $countBlock);
  $logsUse=fopen("logs.txt","w");
  fwrite($logsUse, $time . "\n" . strip_tags($newBlock));
} else {
  $findBlock = 0;
  $newBlock = "<b>Não achamos nenhum bloco ainda, $luckyText data: $time</b>";
  $logsUse=fopen("logs.txt","a");
  fwrite($logsUse,$time . " Nenhum bloco encontrado" . "\n");
}
$msgText[0] = $newBlock;
foreach ($currentMemberEffort as $key => $value) {
  // $msgText[] = "<b>[Nome]</b> " . $currentMember['name'] . ' <b>[Porcentagem]</b> ' .$currentMember['porcent'] ;
  $msgText[$key+2] = $value['name'] . ' => ' . "<b>" . $value['porcent'] . "</b>" ;
  $excel[$key+2] = $value['name'] . ";" . $dateTime . ";" . $value['name'] . ";" . $value['porcent'];
}

curl_close($ch);

// Estamos adicionando na mensagem que será enviada informações do bloco
$allMembers = implode("\n", $msgText);
array_push($excel,strip_tags($newBlock));
$excelSave = implode("\n", $excel);
$fileExcel=fopen("contribuicao.txt","a");
fwrite($fileExcel, $excelSave);

$apiToken = "1818080797:AAHOwkvxIXfHqjZZYf-HYkjJ-yuPLDmAd7Q";
$data = [
    'chat_id' => '@CooperativaSoloErgo',
    'text' => $allMembers,
    'parse_mode' => 'HTML',
];
// Enviando a mensagem no telegram
$telegram = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
if ($findBlock) {
 file_get_contents("https://api.telegram.org/bot$apiToken/sendVideo?chat_id=@CooperativaSoloErgo&video=https://media.giphy.com/media/55modEase0gRwdsi8z/giphy.gif");
}
}
//Loop infinito de 5 em 5 minutos
$time = date('d/m/Y H:i:s');
 while(true) {
   $now = time();
     if($now + 600 > time()){
     FindNewBlock();
     echo "Enviado mensagem telegram $time<br>";
   sleep(600);
    }
 }
FindNewBlock();
?>