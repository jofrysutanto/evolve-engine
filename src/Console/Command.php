<?php
namespace EvolveEngine\Console;

use EvolveEngine\Router\Traits\RouteDependencyResolverTrait;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    public function line($message)
    {
        $this->output->writeln($message);
    }

    public function info($message)
    {
        $this->output->writeln('<info>'.$message.'</info>');
    }

    public function error($message)
    {
        $this->output->writeln('<error>'.$message.'</error>');
    }

    public function success($message)
    {
        $this->output->writeln('<info>'.$message.'</info>');
    }

}