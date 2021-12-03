<?php

namespace supercrafter333\PlotRate;

use MyPlot\events\MyPlotClearEvent;
use MyPlot\events\MyPlotPlayerEnterPlotEvent;
use MyPlot\events\MyPlotResetEvent;
use pocketmine\event\Listener;

/**
 * Class EventListener
 * @package supercrafter333\PlotRate
 */
class EventListener implements Listener
{

    /**
     * @param MyPlotPlayerEnterPlotEvent $event
     */
    public function onPlotEnter(MyPlotPlayerEnterPlotEvent $event)
    {
        $event->cancel(false);
        $player = $event->getPlayer();
        $name = $player->getName();
        $plot = $event->getPlot();
        $pr = PlotRate::getInstance();
        $plotString = $pr->returnPlotString($plot);
        if ($pr->isRated2($plotString) == true) {
            $plotRate = $pr->getPlotRate($plotString);
            if ($plotRate === null) {
                $player->sendTitle("§e☆☆☆☆☆");
            }
            if ($plotRate === 0) {
                $player->sendTitle("§e☆☆☆☆☆");
            } elseif ($plotRate === 1) {
                $player->sendTitle("§e★☆☆☆☆");
            } elseif ($plotRate === 2) {
                $player->sendTitle("§e★★☆☆☆");
            } elseif ($plotRate === 3) {
                $player->sendTitle("§e★★★☆☆");
            } elseif ($plotRate === 4) {
                $player->sendTitle("§e★★★★☆");
            } elseif ($plotRate === 5){
                $player->sendTitle("§e★★★★★");
            }
        }
    }

    /**
     * @param MyPlotClearEvent $event
     */
    public function onPlotClear(MyPlotClearEvent $event)
    {
        $plot = $event->getPlot();
        $pr = PlotRate::getInstance();
        $pr->unratePlot($plot);
    }

    /**
     * @param MyPlotResetEvent $event
     */
    public function onPlotReset(MyPlotResetEvent $event)
    {
        $plot = $event->getPlot();
        $pr = PlotRate::getInstance();
        $pr->unratePlot($plot);
    }
}