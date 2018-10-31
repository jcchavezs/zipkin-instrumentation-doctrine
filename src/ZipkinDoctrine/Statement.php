<?php

namespace ZipkinDoctrine;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Statement as DBALStatement;
use Zipkin\Tracer;
use Zipkin\Tags;

class Statement extends DBALStatement
{
    /**
     * @var Tracer
     */
    private $tracer;

    public function __construct($sql, DBALConnection $conn, Tracer $tracer)
    {
        parent::__construct($sql, $conn);
        $this->tracer = $tracer;
    }

    /**
     * {@inhertidoc}
     */
    public function execute($params = null)
    {
        $span = $this->tracer->nextSpan();
        $span->setName('sql/execute');
        $span->tag(Tags\SQL_QUERY, $this->sql);

        try {
            $span->start();
            $result = parent::execute($params);
            if ($result === false) {
                $span->tag(Tags\ERROR, "1");
            }

            return $result;
        } catch (DBALException $e) {
            $span->tag(Tags\ERROR, $e->getMessage());
            throw $e;
        } finally {
            $span->finish();
        }
    }
}
