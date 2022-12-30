<?php


namespace AhmetAksoy\CustomLogger\Handler;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use AhmetAksoy\CustomLogger\Exception\InvalidSchemaException;

class EloquentHandler extends AbstractProcessingHandler
{
    protected $model;

    private array $columns = [
        'message' => 'text',
        'context' => 'json',
        'level' => 'integer',
        'level_name' => 'string',
        'extra' => 'json',
        'formatted' => 'text'
    ];

    public function __construct($model, $level = Logger::DEBUG, bool $bubble = true)
    {
        if (!is_subclass_of($model, Model::class)) {
            $baseModel = Model::class;
            throw new InvalidArgumentException("${model} class is not subclass of ${baseModel} class");
        }

        $this->model = $model;

        $connectionName = $this->model::getConnectionResolver()->connection()->getConfig()['name'];
        $tableName = with(new $this->model)->getTable();
        $schema = Schema::connection($connectionName);

        foreach ($this->columns as $columnName => $columnType) {
            if (!$schema->hasColumn($tableName, $columnName)) {
                throw new InvalidSchemaException("The ${columnName} column was not found in the ${tableName} table");
            }
            if (!($schema->getColumnType($tableName, $columnName) == $columnType)) {
                throw new InvalidSchemaException("In the ${tableName} table, ${columnName} column is not of type ${columnType}");
            }
        }


        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $model = new $this->model;
        $model->message = $record['message'];
        $model->context = $record['context'];
        $model->level = $record['level'];
        $model->level_name = $record['level_name'];
        $model->extra = $record['extra'];
        $model->formatted = $record['formatted'];
        $model->save();
    }
}
