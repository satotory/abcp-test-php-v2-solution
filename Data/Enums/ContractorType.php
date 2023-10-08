<?php

namespace NW\WebService\References\Operations\Notification\Data\Enums;

enum ContractorType: int
{
    case TYPE_CUSTOMER  = 0;
    /** 
     * с одним типом контрактора не совсем понятно, что из себя представляет тип пользователя, поэтому добавил тип TYPE_COMPANION, которым мог бы быть например магазин, пользующийся услугами приложения 
     * и в случае с ним, например, требуются какие то другие данные в запросе
     */

    case TYPE_COMPANION = 1;
}