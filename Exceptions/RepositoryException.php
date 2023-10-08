<?php

namespace NW\WebService\References\Operations\Notification\Exceptions;

use Exception;

/**  
 * Отлавливание некоторых ошибок принимается командным решением, на уровне договоренности. 
 * Ошибка репозитория в моем примере не позволяет пропустить в прод не валидный репозиторий с неправильной моделью
 */
class RepositoryException extends Exception {}