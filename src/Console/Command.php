<?php
namespace EvolveEngine\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use EvolveEngine\Router\Traits\RouteDependencyResolverTrait;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    use RouteDependencyResolverTrait;

    /**
     * Our core application
     *
     * @var Container
     */
    protected $container;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        if (!($this->signature || $this->description)) {
            return;
        }

        if (!method_exists($this, 'handle')) {
            throw new \InvalidArgumentException("`handle` method needs to be implemented by " . get_class());
        }

        $this
            ->setName($this->signature)
            ->setDescription($this->description)
            ->setHelp('This command allows you to create a user...');

        $this->container = app();
    }

    /**
     * Execute the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->setInput($input)
            ->setOutput($output);

        return $this->callWithDependencies($this, 'handle');
    }

    /**
     * Set input interface
     *
     * @param InputInterface $input
     */
    protected function setInput($input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * Set output interface
     *
     * @param OutputInterface $outpu
     */
    protected function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Write a string as information output.
     *
     * @param  string  $string
     * @return void
     */
    public function info($string)
    {
        $this->line($string, 'info');
    }

    /**
     * Alias for 'info'
     *
     * @param  string  $string
     * @return void
     */
    public function success($string)
    {
        $this->line($string, 'info');
    }

    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     * @param  string|null  $style
     * @return void
     */
    public function line($string, $style = null)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;
        $this->output->writeln($styled);
    }

    /**
     * Write a string as comment output.
     *
     * @param  string  $string
     * @return void
     */
    public function comment($string)
    {
        $this->line($string, 'comment');
    }

    /**
     * Write a string as question output.
     *
     * @param  string  $string
     * @return void
     */
    public function question($string)
    {
        $this->line($string, 'question');
    }

    /**
     * Write a string as error output.
     *
     * @param  string  $string
     * @return void
     */
    public function error($string)
    {
        $this->line($string, 'error');
    }

    /**
     * Write a string as warning output.
     *
     * @param  string  $string
     * @return void
     */
    public function warn($string)
    {
        if (! $this->output->getFormatter()->hasStyle('warning')) {
            $style = new OutputFormatterStyle('yellow');
            $this->output->getFormatter()->setStyle('warning', $style);
        }
        $this->line($string, 'warning');
    }

    /**
     * Alias for 'warn'
     *
     * @param  string  $string
     * @return void
     */
    public function warning($string)
    {
        $this->warn($string);
    }

    /**
     * Write a string in an alert box.
     *
     * @param  string  $string
     * @return void
     */
    public function alert($string)
    {
        $length = \Illuminate\Support\Str::length(strip_tags($string)) + 12;
        $this->comment(str_repeat('*', $length));
        $this->comment('*     '.$string.'     *');
        $this->comment(str_repeat('*', $length));
        $this->output->newLine();
    }
}
