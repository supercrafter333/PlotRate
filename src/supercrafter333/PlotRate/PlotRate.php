<?php

namespace supercrafter333\PlotRate;

use MyPlot\Plot;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use supercrafter333\PlotRate\Commands\EditratingCommand;
use supercrafter333\PlotRate\Commands\PlotRateCommand;
use supercrafter333\PlotRate\Commands\RateCommand;
use supercrafter333\PlotRate\Commands\UnrateCommand;

/**
 * Class PlotRate
 * @package supercrafter333\PlotRate
 */
class PlotRate extends PluginBase
{

    /**
     * That's the version of PlotRate.
     *
     * @var string|float
     */
    public const VERSION = "1.1.2";

    /**
     * @var self
     */
    protected static PlotRate $instance;

    /**
     * On plugin loading. (That's before enabling)
     */
    public function onLoad()
    {
        self::$instance = $this;
        $this->saveResource("config.yml");
        $this->versionCheck(false); //UPDATE: false
    }

    /**
     * On plugin enabling.
     */
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $myPlotCmds = $this->getServer()->getCommandMap()->getCommand('plot');
        $myPlotCmds->loadSubCommand(new RateCommand($this));
        $myPlotCmds->loadSubCommand(new EditratingCommand($this));
        $myPlotCmds->loadSubCommand(new UnrateCommand($this));
        $this->getServer()->getCommandMap()->register("PlotRate", new PlotRateCommand());
    }

    /**
     * On plugin disabling
     */
    public function onDisable()
    {
        $myPlotCmds = $this->getServer()->getCommandMap()->getCommand('plot');
        $myPlotCmds->unloadSubCommand("rate");
        $myPlotCmds->unloadSubCommand("editrating");
        $myPlotCmds->unloadSubCommand("unrate");
    }

    /**
     * Check the version of PlotRate.
     *
     * @param bool $update
     */
    private function versionCheck(bool $update = true)
    {
        if (!$this->getConfig()->exists("version") || $this->getConfig()->get("version") !== self::VERSION) {
            if ($update == true) {
                $this->getLogger()->debug("OUTDATED CONFIG.YML!! You config.yml is outdated! Your config.yml will automatically updated!");
                if (file_exists($this->getDataFolder() . "oldConfig.yml")) unlink($this->getDataFolder() . "oldConfig.yml");
                rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "oldConfig.yml");
                $this->saveResource("config.yml");
                $this->getLogger()->debug("config.yml Updated for version: §b" . (self::VERSION) . "");
            } else {
                $this->getLogger()->warning("Your config.yml is outdated but that's not so bad.");
            }
        }
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }

    /**
     * @return Config
     */
    public function getRateList(): Config
    {
        return new Config($this->getDataFolder() . "plotRates.yml", Config::YAML);
    }

    /**
     * @param Plot $plot
     * @return string
     */
    public function returnPlotString(Plot $plot): string
    {
        $arr = [$plot->X, $plot->Z, $plot->levelName];
        return '"' . implode(":", $arr) . '"';
        //return $plot->X . ':' . $plot->Z . ':' . $plot->levelName;
    }

    /**
     * @param string $plotString
     * @return array
     */
    private function getPlotStringVals(string $plotString): array
    {
        return explode(':', $plotString);
    }

    /**
     * @param string $plotString
     * @return mixed|null
     */
    public function getPlotX(string $plotString)
    {
        if ($this->isRated2($plotString)) return $this->getPlotStringVals($plotString)[0];
        return null;
    }

    /**
     * @param string $plotString
     * @return mixed
     */
    public function getPlotZ(string $plotString): mixed
    {
        if ($this->isRated2($plotString)) return $this->getPlotStringVals($plotString)[1];
        return null;
    }

    /**
     * @param string $plotString
     * @return mixed
     */
    public function getPlotLevelName(string $plotString): mixed
    {
        if ($this->isRated2($plotString)) return $this->getPlotStringVals($plotString)[2];
        return null;
    }

    /**
     * @param string $plotString
     * @return mixed
     */
    public function getPlotRate(string $plotString): mixed
    {
        if ($this->isRated2($plotString)) return $this->getRateList()->get($plotString);
        return null;
    }

    /**
     * @param Plot $plot
     * @return bool
     */
    public function isRated(Plot $plot): bool
    {
        return $this->getRateList()->exists($this->returnPlotString($plot));
    }

    /**
     * @param string $plotString
     * @return bool
     */
    public function isRated2(string $plotString): bool
    {
        return $this->getRateList()->exists($plotString);
    }

    /**
     * @param Plot $plot
     * @param int $rateStars
     */
    public function ratePlot(Plot $plot, int $rateStars): void
    {
        $rateList = $this->getRateList();
        $plotString = $this->returnPlotString($plot);
        $rateList->set($plotString, $rateStars);
        $rateList->save();
    }

    /**
     * @param Plot $plot
     * @return bool
     */
    public function unratePlot(Plot $plot): bool
    {
        if (!$this->isRated($plot)) return false;
        $rateList = $this->getRateList();
        $plotString = $this->returnPlotString($plot);
        $rateList->remove($plotString);
        $rateList->save();
        return true;
    }
}
