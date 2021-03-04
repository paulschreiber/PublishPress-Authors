<?php

namespace Steps;


trait Debug
{
    /**
     * @Then I take a screenshot named :name
     */
    public function iTakeAScreenshotNamed($name)
    {
        $this->makeScreenshot($name);
    }
}
