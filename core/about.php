<?php include("header.php"); ?>

<?php include("navigation.php"); ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>About<small>eurysco <?= include("version.phtml")?></small></h1>
			<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-about-button big page-back"></a>
		</div>
	</div>
</div>

<br />

<div class="page" id="page-index">
	<div class="page-region">
		<div class="page-region-content">
			<div class="grid">
				<div class="row">
		            <div class="span1"></div>
		            <div class="span10">
					
                    <a href="http://www.eurysco.com"><h2>eurysco: Heuristic System Control <?= include("version.phtml")?></h2></a>
					Freely available from <a href="http://www.eurysco.com">http://www.eurysco.com</a>
                    <h3>GNU General Public License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\eurysco.txt';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

                    <a href="http://php.net"><h2>PHP: Hypertext Preprocessor 5.5.30 (x64)</h2></a>
					This product includes PHP software, freely available from <a href="http://www.php.net/software">http://www.php.net/software</a>
                    <h3>MIT License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\php_5.5.30.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

                    <a href="http://php.net"><h2>PHP: Hypertext Preprocessor 5.5.30 (x86)</h2></a>
					This product includes PHP software, freely available from <a href="http://www.php.net/software">http://www.php.net/software</a>
                    <h3>MIT License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\php_5.5.30.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

                    <a href="http://php.net"><h2>PHP: Hypertext Preprocessor 5.4.32 (x86)</h2></a>
					This product includes PHP software, freely available from <a href="http://www.php.net/software">http://www.php.net/software</a>
                    <h3>MIT License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\php_5.4.32.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

                    <a href="http://php.net"><h2>Apache HTTP Server 2.4.10</h2></a>
					This product includes Apache software<br />freely available from <a href="http://httpd.apache.org">http://httpd.apache.org</a> and from <a href="http://www.apachehaus.com">http://www.apachehaus.com</a>
                    <h3>Apache License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\apache_2.4.10.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

                    <a href="http://metroui.org.ua"><h2>Metro UI CSS 0.95-a</h2></a>
					This product is styled by <a href="https://github.com/olton/Metro-UI-CSS/blob/master/LICENSE">&copy; 2012-2013 Metro UI CSS</a>
                    <h3>MIT License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\metro-ui-css_0.95-a.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

                    <a href="https://jquery.org"><h2>jQuery 1.9.1</h2></a>
					This product includes <a href="https://jquery.org">jQuery JavaScript Library</a>
                    <h3>MIT License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\jquery_1.9.1.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

					<a href="http://phpliteadmin.googlecode.com"><h2>phpLiteAdmin 1.9.5</h2></a>
					This product includes <a href="http://phpliteadmin.googlecode.com">PHP-based admin tool to manage SQLite2 and SQLite3 databases on the web</a>
					<h3>GNU General Public License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\phpliteadmin_1.9.5.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

					<a href="http://www.plupload.com"><h2>Plupload 2.1.1</h2></a>
					This product includes <a href="http://www.plupload.com/license">Plupload - Multi-Runtime File Uploader</a>
					<h3>GNU General Public License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\plupload_2.1.1.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

					<a href="http://yankoa.deviantart.com"><h2>MetroStation Icons Pack 2.20</h2></a>
					This product includes <a href="http://yankoa.deviantart.com">MetroStation Icons Pack</a>
					<h3>Creative Commons Attribution-Noncommercial-Share Alike 3.0 License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\metrostation-icons-pack_2.20.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

					<a href="http://www.php-firewall.info"><h2>PHP Firewall 1.0.3</h2></a>
					This product includes <a href="http://www.php-firewall.info">PHP Firewall - Free Security System for WebSite</a>
					<h3>Generic Open Source License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\php-firewall_1.0.3.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

					<a href="http://www.phpkode.com/scripts/item/windows-registry-classes"><h2>Windows Registry Classes 1.0</h2></a>
					This product includes <a href="http://www.phpkode.com/scripts/item/windows-registry-classes">Windows Registry PHP Class</a>
					<h3>BSD License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\windows-registry-classes_1.0.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

					<a href="http://qtweb.net"><h2>Chromium Portable 39.0.2145.4</h2></a>
					This product includes <a href="http://www.chromium.org/getting-involved/dev-channel">Chromium Portable</a>
					<h3>Open Source</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\chromiumportable_39.0.2145.4.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

					<a href="http://www.7-zip.org"><h2>7-Zip 15.12</h2></a>
					This product includes <a href="http://www.7-zip.org">7-Zip</a>
					<h3>GNU Lesser General Public License</h3>
                    <div class="input-control textarea">
                    <textarea id="licenseoutput" name="licenseoutput" readonly="readonly" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:125px; font-weight:normal; overflow-x:hidden;"><?php
						$name = $_SERVER['DOCUMENT_ROOT'] . '\\licenses\\7-Zip_15.12.license';
						$licenseoutput = 'License File Not Found...';
						if (file_exists($name) && is_readable($name)) {
							$filearr = file($name);
							$lastlines = array_slice($filearr, -10000);
							$licenseoutput = '';
							foreach ($lastlines as $lastline) {
								$licenseoutput = $licenseoutput . $lastline;
							}
						}
						echo $licenseoutput;
					?></textarea>
                    </div>
                    <br />
                    <br />

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>