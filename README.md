<h1># mining_solo_bot</h1>

<h2>This repository was made for a mining cooperative:</h2>

<h3>This involves taking all worker data from a specific wallet within the Herominers api, calculating it based on difficulty and dividing the earnings among all members.</h3>

<h3>It is posted in a telegram channel how many % each worker has of the block and also when this block is found.
(so the reward is split)</h3>

<h3>A maintenance fee is also subtracted from the total amount.</h3>

<h2>To use just change the wallet:</h2>
9ghYV3v4sKWdRFgJ8hTgh5V61s5D1iNhbzAEW4KuUT6USWhRYPa

These are the lines:
curl_setopt($ch, CURLOPT_URL, 'https://ergo.herominers.com/api/stats_address?address=9ghYV3v4sKWdRFgJ8hTgh5V61s5D1iNhbzAEW4KuUT6USWhRYPa&recentBlocksAmount=20&longpoll=false');
$headers = array();
$headers[] = 'cookie: SLG_GWPT_Show_Hide_tmp=1; SLG_wptGlobTipTmp=1; mining_address=9ghYV3v4sKWdRFgJ8hTgh5V61s5D1iNhbzAEW4KuUT6USWhRYPa';
