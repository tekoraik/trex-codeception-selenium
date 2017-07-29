<?php
namespace trex\codeception\selenium;

use \Codeception\Events;

class SeleniumExtension extends \Codeception\Extension
{
    // list events to listen to
    // Codeception\Events constants used to set the event

    public static $events = array(
        Events::SUITE_AFTER  => 'afterSuite',
        Events::SUITE_BEFORE => 'beforeSuite',
        Events::STEP_BEFORE => 'beforeStep',
        Events::TEST_FAIL => 'testFailed',
        Events::RESULT_PRINT_AFTER => 'print',
    );

    /**
     * @var resource
     */
    private $daemon;
    private $pipes;

    // methods that handle events

    public function afterSuite(\Codeception\Event\SuiteEvent $e) {
        if ($this->suiteName($e) === 'acceptance') {
            $this->killSeleniumServer();
        }
    }

    public function beforeSuite(\Codeception\Event\SuiteEvent $e) {
        if ($this->suiteName($e) === 'acceptance') {
            $this->execSeleniumServer();
        }
    }

    public function beforeStep(\Codeception\Event\StepEvent $e) {}

    public function testFailed(\Codeception\Event\FailEvent $e) {}

    public function print(\Codeception\Event\PrintResultEvent $e) {}

    /**
     * @param \Codeception\Event\SuiteEvent $e
     *
     * @return string
     */
    private function suiteName(\Codeception\Event\SuiteEvent $e)
    {
        return $e->getSuite()->getBaseName();
    }

    private function execSeleniumServer()
    {
        exec('ps aux | grep selenium | awk \'{print $2}\' | xargs kill -9');

        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin es una tubería usada por el hijo para lectura
            //1 => array("pipe", "w"),  // stdout es una tubería usada por el hijo para escritura
            1 => array("file", "/tmp/output-selenium.log", "w"),
            //2 => array("pipe", "w") // stderr es un fichero para escritura
            2 => array("file", "/tmp/output-selenium-err.log", "w"),
        );

        $this->daemon = proc_open('java -jar ' . __DIR__ . '/../resources/selenium-server-standalone-3.1.0.jar', $descriptorspec, $this->pipes);
        echo "Running selenium\n";
        sleep(4);
    }

    private function killSeleniumServer()
    {
        $this->killProcess($this->daemon);
    }

    private function killProcess($process)
    {
        $pipes = $this->pipes;
        $status = proc_get_status($process);
        //close all pipes that are still open
        //get the parent pid of the process we want to kill
        $ppid = $status['pid'];
        //use ps to get all the children of this process, and kill them
        $pids = preg_split('/\s+/', exec("ps -o pid --no-heading --ppid $ppid"));
        foreach($pids as $pid) {
            if(is_numeric($pid)) {
                echo "Killing $pid\n";
                posix_kill($pid, 9); //9 is the SIGKILL signal
            }
        }

        proc_close($process);
    }
}