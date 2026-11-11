<?php

namespace onezerotrash\Firebird;

use onezerotrash\Firebird\Query\Builder as FirebirdQueryBuilder;
use onezerotrash\Firebird\Query\Grammars\FirebirdGrammarDialect1 as FirebirdQueryGrammarDialect1;
use onezerotrash\Firebird\Query\Grammars\FirebirdGrammarDialect3 as FirebirdQueryGrammarDialect3;
use onezerotrash\Firebird\Query\Processors\FirebirdProcessor as FirebirdQueryProcessor;
use onezerotrash\Firebird\Schema\Builder as FirebirdSchemaBuilder;
use onezerotrash\Firebird\Schema\Grammars\FirebirdGrammar as FirebirdSchemaGrammar;
use Illuminate\Database\Connection as DatabaseConnection;

class FirebirdConnection extends DatabaseConnection
{
    /**
     * Get the default query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\Grammar
     */
    protected function getDefaultQueryGrammar()
    {
        // # ALTERADO POR CODEX: Laravel12/13 exige a ligação no construtor da grammar.
        if ($this->getDialectVersion() == '1') {
            return new FirebirdQueryGrammarDialect1($this);
        }
        
        return new FirebirdQueryGrammarDialect3($this);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Illuminate\Database\Query\Processors\Processor
     */
    protected function getDefaultPostProcessor()
    {
        return new FirebirdQueryProcessor;
    }

    /**
     * Get a schema builder instance for this connection.
     *
     * @return \Firebird\Schema\Builder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new FirebirdSchemaBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \Firebird\Schema\Grammars\FirebirdGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        // # ALTERADO POR CODEX: a grammar atual obtém o prefixo através da ligação.
        return new FirebirdSchemaGrammar($this);
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Firebird\Query\Builder
     */
    public function query()
    {
        return new FirebirdQueryBuilder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }

    /**
     * Execute a stored procedure.
     *
     * @param  string  $procedure
     * @param  array  $values
     * @return \Illuminate\Support\Collection
     */
    public function executeProcedure($procedure, array $values = [])
    {
        return $this->query()->fromProcedure($procedure, $values)->get();
    }

    /**
     * Get Firebird dialect version that should be used when compiling queries.
     *
     * @return string
     */
    protected function getDialectVersion()
    {
        if (! array_key_exists('dialect', $this->config)) {
            return '3';
        }

        return $this->config['dialect'];
    }
}
