<?php

namespace Autoframe\Tenant;

//TODO de mergiuit cu clasa de log pt ury ca am acolo timings
class AfrEvent
{
    const AFR_EVENT = 'afr.event'; //applies on any event
    const AFR_BOOTSTRAP = 'afr.bootstrap';
    const DB_ACTION_START = 'db.action.start0';
    const DB_QUERY_START = 'db.query.start1';
    const DB_QUERY_END = 'db.query.end2';
    const DB_FETCH_START = 'db.fetch.start3';
    const DB_FETCH_DONE = 'db.fetch.done4';
    const DB_ACTION_DONE = 'db.action.done5';

	const MODULE_READ_START = 'mod.read.start';
	const MODULE_READ_END = 'mod.read.end';
	const MODULE_LOAD = 'mod.load';

    const ROUTER_BEFORE_ROUTING_0 = 'router.before.routing0';
    const ROUTER_ON_MIDDLEWARE_1 = 'router.on.middleware1';
    const ROUTER_ON_CODE_2 = 'router.on.code2';
    const ROUTER_ON_AFTER_3 = 'router.on.after3';
    const ROUTER_BEFORE_VIEW_4 = 'router.before.view4';
    const ROUTER_VIEW_RENDER_START_5 = 'router.view.render.start5';
    const ROUTER_VIEW_RENDER_DONE_6 = 'router.view.render.done6';
    const ROUTER_VIEW_OUTPUT_START_7 = 'router.view.output.start7';
    const ROUTER_VIEW_OUTPUT_DONE_8 = 'router.view.output.done8';


    const CACHE_READ_START = 'cache.read.start';
    const CACHE_READ_END = 'cache.read.end';
    const CACHE_HIT = 'cache.hit';
    const CACHE_MISS = 'cache.miss';


    protected static array $aEvents = [];
    protected static array $aOnEventClosures = [];
    protected static array $aWildcards = [];

    //todo de facut aici cu stack trace / timings / log? single responsability
    public function __construct(string $sEvent)
    {
        self::addEvent(...func_get_args());
    }

    public static function addEvent(string $sEvent): array
    {
        $allArgs = func_get_args();
        $allArgs[] = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5), 1);
        self::$aEvents[] = $allArgs;
        return self::callEventClosure($sEvent, $allArgs);
    }

    protected static function callEventClosure(string $sEvent, array $aAllData): array
    {
        $aReturn = [];
        if (!empty(self::$aOnEventClosures[$sEvent]) && $sEvent !== self::AFR_EVENT) {
            foreach (self::$aOnEventClosures[$sEvent] as $i => $callable) {
                $aReturn[$i] = self::$aOnEventClosures[$sEvent][$i](...$aAllData);
            }
        }

        if (!empty(self::$aOnEventClosures[self::AFR_EVENT])) {
            foreach (self::$aOnEventClosures[self::AFR_EVENT] as $i => $callable) {
                $aReturn[$i] = self::$aOnEventClosures[self::AFR_EVENT][$i](...$aAllData);
            }
        }
        return $aReturn;
    }

    public static function addOnEventClosure(string $sEvent, callable $closure, string $sWildcard = ''): void
    {

        // AFR_WILDCARD_STARTS_WITH = 'afr.*'; //applies on any event starting with afr.*
        // AFR_WILDCARD_ENDS_WITH = '*afr'; //applies on any event ending with *afr
        // AFR_WILDCARD_CONTAINS = '*afr*'; //applies on any event containing the substring
        //TODO wildcards
        if (strpos($sWildcard, '*') !== false) {
            self::$aWildcards[$sWildcard] = $closure;
            return;
        }

        if (empty(self::$aOnEventClosures[$sEvent])) {
            self::$aOnEventClosures[$sEvent] = [$closure];
        } else {
            self::$aOnEventClosures[$sEvent][] = $closure;
        }
    }

    public static function getEvents(): array
    {
        return self::$aEvents;
    }

    public static function getOnEventClosures(): array
    {
        return self::$aOnEventClosures;
    }
}