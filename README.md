**Acceptance test configuration example with recommendable extension PhpBuiltinServer**

<pre>
class_name: AcceptanceTester
extensions:
    enabled:
        - Codeception\Extension\PhpBuiltinServer
        - trex\codeception\selenium\SeleniumExtension
    config:
        Codeception\Extension\PhpBuiltinServer:
            hostname: localhost
            port: 8000
            autostart: true
            documentRoot: web/
            startDelay: 1
modules:
    enabled:
        - \Helper\Acceptanc
        - WebDriver:
            url: "http://localhost:8000/index-test.php"
            browser: chrome
            capabilities:
                    chromeOptions:
                      args: ["--headless", "--disable-gpu", "--disable-extensions"]
</pre>