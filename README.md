**Acceptance test configuration example with recommendable extension PhpBuiltinServer**

<pre>
class_name: AcceptanceTester
extensions:
    enabled:
        - Codeception\Extension\PhpBuiltinServer
        - tests\utils\extensions\selenium\SeleniumExtension
    config:
        Codeception\Extension\PhpBuiltinServer:
            hostname: localhost
            port: 8000
            autostart: true
            documentRoot: web/
            startDelay: 1
modules:
    enabled:
        - \Helper\Acceptance
        - WebDriver:
            url: "http://localhost:8000/index-test.php"
            browser: chrome
            capabilities:
                    chromeOptions:
                      args: ["--headless", "--disable-gpu", "--disable-extensions"]
        - Yii2:
            part: [orm, email, fixtures]
            entryScript: index-test.php
</pre>