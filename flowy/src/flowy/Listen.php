<?php
namespace flowy;

use pocketmine\event\Event;
use pocketmine\utils\Utils;

class Listen
{
    /** @var string[] */
    protected $events;

    /** @var callable[] */
    protected $filters;

    public function __construct(array $events)
    {
        if (count($events) === 0) {
            throw new \LogicException();
        }
        foreach ($events as $event) {
            $class = new \ReflectionClass($event);
            $tags = Utils::parseDocComment($class->getDocComment());
            if (!$class->isSubclassOf(Event::class)) {
                throw new \LogicException();
            }
            if ($class->isAbstract() && !isset($tags["allowHandle"])) {
                throw new \LogicException();
            }
        }

        $this->events = $events;
        $this->filters = [];
    }

    /** @return string[] */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function filter(callable $filter): Listen
    {
        $this->addFilter($filter);
        return $this;
    }

    public function addFilter(callable $filter): void
    {
        $this->filters[] = $filter;
    }

    public function match(Event $event): bool
    {
        if (!in_array(get_class($event), $this->events)) {
            return false;
        }
        foreach ($this->filters as $filter) {
            if ($filter($event) === false) {
                return false;
            }
        }
        return true;
    }
}