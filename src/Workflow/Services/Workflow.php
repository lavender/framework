<?php
namespace Lavender\Workflow\Services;

use Lavender\Workflow\Exceptions\StateException;
use Lavender\Workflow\Interfaces\WorkflowInterface;
use Lavender\Workflow\Interfaces\RendererInterface;
use Lavender\Workflow\Interfaces\RepositoryInterface;

class Workflow implements WorkflowInterface
{
    /**
     * Workflow repository
     * @var Repository
     */
    private $repo;

    /**
     * Renderable object
     * @var State\Renderer
     */
    private $renderer;

    /**
     * State configuration array     *
     * @var array
     */
    private $states;

    /**
     * Workflow session from db
     * @var \stdClass
     */
    protected $session;

    /**
     * Store current state
     * @var string
     */
    public $state;

    /**
     * Workflow identifier
     * @var string
     */
    protected $workflow;

    /**
     * Merged config for this workflow
     * @var array
     */
    protected $config;


    /**
     * Instantiate a clean workflow object
     *
     * @param RepositoryInterface $repo
     * @param RendererInterface $renderer
     * @throws \InvalidArgumentException
     */
    public function __construct(RepositoryInterface $repo, RendererInterface $renderer)
    {
        $this->renderer = $renderer;

        $this->repo = $repo;

        $this->repo->model($this);
    }


    /**
     * Register the current workflow.
     *
     * @param $workflow
     * @param array $config
     * @return Model
     */
    public function register($workflow, array $config)
    {
        $this->workflow = $workflow;

        $this->config = $config;

        return $this;
    }



    public function handle($state)
    {
        // start the session
        if(!$this->session) $this->findSession();

        if($state !== $this->state){

            // something went wrong
            throw new \InvalidArgumentException(
                sprintf(
                    "State requested \"%s\" does not match current state \"%s\" on workflow \"%s\"",
                    $state,
                    $this->state,
                    $this->workflow
                )
            );

        }

        // load the current state's renderer
        if($config = $this->config[$state]){

            // handle the request
            if($request = \Input::all()){

                if(isset($config['fields'])){

                    $fields = $config['fields'];

                    // first we flash the input into session
                    $this->flashInput($fields);

                    // next we make sure all required fields are set
                    $this->handleFields($fields, $request);
                }

                // then we execute the after filters
                if(isset($config['after'])) $this->handleAfter($config['after'], $request);

            }

        } else {

            // something went wrong
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid request \"%s\" not found in workflow %s",
                    $this->state,
                    $this->workflow
                )
            );

        }
    }

    /**
     * Flash only fields where flash = true
     *
     * @param $fields
     */
    protected function flashInput($fields)
    {
        \Input::flashOnly(array_keys(array_where($fields, function($key, $config){

            return $config['flash'];

        })));
    }

    protected function handleAfter($filters, $request)
    {
        foreach($filters as $after => $filter){

            if($model = new $filter['class']){

                $model->handle($request);

            }

        }

    }

    protected function handleFields($fields, $request)
    {
        $rules = [];

        foreach($fields as $field => $data){

            if($data['validate']) $rules[$field] = $data['validate'];

        }

        $validator = \Validator::make($request, $rules);

        if ($validator->fails()){

            throw new StateException(
                sprintf(
                    "Validator failed for workflow %s",
                    $this->workflow
                ),
                $validator
            );
        }
    }

    /**
     * Render the current workflow state
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function render()
    {
        // load current session
        if(!$this->session) $this->findSession();

        // load current state
        $this->renderer->make(
            $this->workflow,
            $this->state,
            $this->config[$this->state]
        );

        // render current state
        return $this->renderer->render();
    }


    public function firstSession()
    {
        // Find an existing workflow session or create new
        $this->session = $this->repo->first();

        // Set the current state
        $this->state = $this->session->state;
    }

    public function nextSession()
    {
        // Find an existing workflow session or create new
        $this->session = $this->repo->next();

        // Set the current state
        $this->state = $this->session->state;

        // use custom redirect
        return isset($this->config[$this->state]['redirect']) ?
            \Redirect::to($this->config[$this->state]['redirect']) : false;
    }

    public function findSession()
    {
        app('workflow.session')->setId($this->workflow);

        // Find an existing workflow session or create new
        $this->session = $this->repo->findOrNew();

        // Set the current state
        $this->state = $this->session->state;
    }

    public function getStates($include_config = false)
    {
        if(!isset($this->states)){

            $states = $this->config;

            sort_children($states);

            $this->states = $states;

        }

        if($include_config) return $this->states;

        return array_keys($this->states);
    }

    public function defaultState()
    {
        return $this->getStates()[0];
    }

    public function nextState()
    {
        $curr = array_search($this->state, $states = $this->getStates());

        if(isset($states[$curr + 1])) return $states[$curr + 1];

        return $this->state;
    }

    public function hasState($state)
    {
        return in_array($state, $this->getStates());
    }

    public function getWorkflow()
    {
        return $this->workflow;
    }

    public function getState()
    {
        return $this->state;
    }

}