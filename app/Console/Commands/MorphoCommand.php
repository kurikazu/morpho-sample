<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Jobs\MorphoJob as MorphoJob;

class MorphoCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'morpho';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        //
        try {
            dispatch(new MorphoJob($this->argument('filename')));

        } catch (Exception $e) {
            $messages = $e->getMessageProvider()->getMessageBag()->all();
            foreach($messages as $message) {
                $this->error($message);
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['filename', InputArgument::REQUIRED, 'input file name'],
        ];
    }
}
