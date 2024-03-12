<?php

/**
 * This file is part of the ramsey/uuid-doctrine library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\ParameterType;

use function enum_exists;
use function function_exists;

if (function_exists('enum_exists') && enum_exists(ParameterType::class)) {
    /**
     * @internal
     */
    trait GetBindingTypeImplementation
    {
        public function getBindingType(): ParameterType
        {
            return ParameterType::BINARY;
        }
    }
} else {
    /**
     * @internal
     */
    trait GetBindingTypeImplementation
    {
        public function getBindingType(): int
        {
            return ParameterType::BINARY;
        }
    }
}
