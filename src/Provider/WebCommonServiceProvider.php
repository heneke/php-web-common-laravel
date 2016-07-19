<?php
namespace Heneke\Web\Common\Laravel\Provider;

use Heneke\Web\Common\Request\LimitOffsetRequest;
use Heneke\Web\Common\Request\PageableRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

use Psr\Http\Message\ServerRequestInterface;

use Heneke\Web\Common\Request\LimitOffsetInterface;
use Heneke\Web\Common\Request\LimitOffsetResolver;
use Heneke\Web\Common\Request\PageableInterface;
use Heneke\Web\Common\Request\SortableInterface;
use Heneke\Web\Common\Request\PageableResolver;
use Heneke\Web\Common\Request\SortableResolver;
use Heneke\Web\Common\Request\SortResolver;

class WebCommonServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(LimitOffsetResolver::class, function (Application $app) {
            return new LimitOffsetResolver(new LimitOffsetRequest($this->getDefaultLimit(), $this->getDefaultOffset()), $app->make(SortResolver::class), $this->getParameterLimit(), $this->getParameterOffset());
        });
        $this->app->singleton(PageableResolver::class, function (Application $app) {
            return new PageableResolver(new PageableRequest($this->getDefaultPageNumber(), $this->getDefaultPageSize()), $app->make(SortResolver::class), $this->getParameterPage(), $this->getParameterSize());
        });
        $this->app->singleton(SortableResolver::class, function (Application $app) {
            return new SortableResolver($this->getSortResolver());
        });
        $this->app->singleton(SortResolver::class, function (Application $app) {
            return new SortResolver($this->getParameterSort());
        });

        $this->app->bind(LimitOffsetInterface::class, function (Application $app) {
            return $this->getLimitOffsetResolver()->resolveWithDefault($this->getPsr7ServerRequest());
        });
        $this->app->bind(PageableInterface::class, function (Application $app) {
            return $this->getPageableResolver()->resolveWithDefault($this->getPsr7ServerRequest());
        });
        $this->app->bind(SortableInterface::class, function (Application $app) {
            return $this->getSortableResolver()->resolveSilently($this->getPsr7ServerRequest());
        });
    }

    protected function getDefaultLimit()
    {
        return 25;
    }

    protected function getDefaultOffset()
    {
        return 0;
    }

    protected function getDefaultPageNumber()
    {
        return 1;
    }

    protected function getDefaultPageSize()
    {
        return 25;
    }

    protected function getParameterLimit()
    {
        return 'limit';
    }

    protected function getParameterOffset()
    {
        return 'offset';
    }

    protected function getParameterPage()
    {
        return 'page';
    }

    protected function getParameterSize()
    {
        return 'size';
    }

    protected function getParameterSort()
    {
        return 'sort';
    }

    /**
     * @return LimitOffsetResolver
     */
    protected function getLimitOffsetResolver()
    {
        return $this->app->make(LimitOffsetResolver::class);
    }

    /**
     * @return PageableResolver
     */
    protected function getPageableResolver()
    {
        return $this->app->make(PageableResolver::class);
    }

    /**
     * @return SortResolver
     */
    protected function getSortResolver()
    {
        return $this->app->make(SortResolver::class);
    }

    /**
     * @return SortableResolver
     */
    protected function getSortableResolver()
    {
        return $this->app->make(SortableResolver::class);
    }

    /**
     * @return ServerRequestInterface
     */
    protected function getPsr7ServerRequest()
    {
        return $this->app->make(ServerRequestInterface::class);
    }
}
