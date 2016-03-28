<?
require_once('init.php');

EasyRdf_Namespace::set('solid', 'http://www.w3.org/ns/solid/terms#');

function get($url){
  $errors = array();
  $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	$ct = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	return array('source'=>$data, 'ct'=>$ct, 'status'=>$status);
}

if(isset($_GET['uri']) && !empty($_GET['uri'])){
  $response = get($_GET['uri']);
}

?>

<!doctype html>
<html>
  <head>
    <title>Midi-cholorian</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <header>
      <img src="yoda.png" width="100" />
      <h1>Midi-Chlorian</h1>
      <p>Server and client in a symbiotic relationship.</p>
    </header>
    <form>
      <p>This is a sample app to demonstrate how serverside code can be used to alleviate the JS-required constraint for the retrieval of public resources on <a href="https://github.com/solid">Solid</a> servers. Basically the server tries to fetch the resource and if it can't get it because there's access control set, the clientside JS tries to authenticate you then fetch it. So JS is required <em>only</em> for non-public resources, which is better than being required for everything.</p>
      <p><label for="uri">URI of something to display</label> <input type="text" name="uri" placeholder="https://solid.pod/path/to/something" id="uri" <?=isset($_GET['uri']) ? 'value="'.$_GET['uri'].'"' : ''?> /></p>
      <p><input type="submit" id="get" value="Get" /></p>
    </form>
    <?if(isset($response)):?>
      <div id="result">
        <h2>Response</h2>
        <p>Status: <span id="status"><?=$response['status']?></span><?=($response['status'] == "401" || $response['status'] == "403") ? '<span class="noscript"> (Enable JS to authenticate and retry)</span>' : ''?></p>
        <p>Content-type: <span id="ct"><?=$response['ct']?></span></p>
        <pre id="response">
          <?=htmlentities($response['source']); ?>
        </pre>
      </div>
      <footer>
        <ul>
          <li><a href="https://github.com/linkeddata/midichlorian">Source</a></li>
          <li><a href="https://github.com/linkeddata/midichlorian/issues">Issues</a></li>
          <li><a href="https://github.com/solid">Solid</a></li>
          <li><a href="https://github.com/solid/solid.js">Solid.js</a></li>
          <li><a href="https://easyrdf.org">EasyRDF (PHP)</a></li>
        </ul>
      </footer>
    <?endif?>
    <script src="https://solid.github.io/releases/rdflib.js/rdflib-0.5.0.min.js"></script>
    <script src="https://solid.github.io/releases/solid.js/solid-0.13.0.min.js"></script>
    <script>
      var solid = require('solid')
      
      var drop = document.getElementsByClassName('noscript');
      while(drop[0]) {
        drop[0].parentNode.removeChild(drop[0]);
      }
      
      var showUser = function(webId){
        var first = document.getElementById('result').querySelector('h2')
        var update = document.createElement('p')
        update.innerText = 'Authenticated as ' + webId
        first.parentNode.insertBefore(update, first.nextSibling);
      }
      
      var getResource = function(url){
        solid.web.get(url).then(
          function(response) {
            console.log(response)
            document.getElementById('status').innerText = response.xhr.status
            document.getElementById('ct').innerText = response.contentType()
            document.getElementById('response').innerText = response.raw()
            showUser(response.user)
          }
        ).catch(
          function(err) {
            console.log(err) // error object
            // ...
          }
        )
      }
      
      if(document.getElementById('status')){
        var responseStatus = document.getElementById('status').innerText;
        var url = document.getElementById('uri').value
        switch(responseStatus){
          case "404": case "0":
            console.log('not found')
            break
          case "401": case "403":
            console.log('trying with auth')
            getResource(url)
            break
          default:
            console.log('do nothing')
            console.log(responseStatus)
            break
        }
      }
    </script>
  </body>
</html>