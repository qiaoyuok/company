<?php
/*
 * Copyright (c) 2017-2018 THL A29 Limited, a Tencent company. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TencentCloud\Live\V20180801\Models;
use TencentCloud\Common\AbstractModel;

/**
 * @method string getStartTime() 获取起始时间，北京时间，
格式：yyyy-mm-dd HH:MM:SS。
 * @method void setStartTime(string $StartTime) 设置起始时间，北京时间，
格式：yyyy-mm-dd HH:MM:SS。
 * @method string getEndTime() 获取结束时间，北京时间，
格式：yyyy-mm-dd HH:MM:SS。
注：EndTime 和 StartTime 只支持最近1天的数据查询。
 * @method void setEndTime(string $EndTime) 设置结束时间，北京时间，
格式：yyyy-mm-dd HH:MM:SS。
注：EndTime 和 StartTime 只支持最近1天的数据查询。
 * @method integer getGranularity() 获取查询粒度：
1-1分钟粒度。
 * @method void setGranularity(integer $Granularity) 设置查询粒度：
1-1分钟粒度。
 * @method string getStatType() 获取是，可选值包括”4xx”,”5xx”，支持”4xx,5xx”等这种混合模式。
 * @method void setStatType(string $StatType) 设置是，可选值包括”4xx”,”5xx”，支持”4xx,5xx”等这种混合模式。
 * @method array getPlayDomains() 获取播放域名列表。
 * @method void setPlayDomains(array $PlayDomains) 设置播放域名列表。
 */

/**
 *DescribePlayErrorCodeDetailInfoList请求参数结构体
 */
class DescribePlayErrorCodeDetailInfoListRequest extends AbstractModel
{
    /**
     * @var string 起始时间，北京时间，
格式：yyyy-mm-dd HH:MM:SS。
     */
    public $StartTime;

    /**
     * @var string 结束时间，北京时间，
格式：yyyy-mm-dd HH:MM:SS。
注：EndTime 和 StartTime 只支持最近1天的数据查询。
     */
    public $EndTime;

    /**
     * @var integer 查询粒度：
1-1分钟粒度。
     */
    public $Granularity;

    /**
     * @var string 是，可选值包括”4xx”,”5xx”，支持”4xx,5xx”等这种混合模式。
     */
    public $StatType;

    /**
     * @var array 播放域名列表。
     */
    public $PlayDomains;
    /**
     * @param string $StartTime 起始时间，北京时间，
格式：yyyy-mm-dd HH:MM:SS。
     * @param string $EndTime 结束时间，北京时间，
格式：yyyy-mm-dd HH:MM:SS。
注：EndTime 和 StartTime 只支持最近1天的数据查询。
     * @param integer $Granularity 查询粒度：
1-1分钟粒度。
     * @param string $StatType 是，可选值包括”4xx”,”5xx”，支持”4xx,5xx”等这种混合模式。
     * @param array $PlayDomains 播放域名列表。
     */
    function __construct()
    {

    }
    /**
     * 内部实现，用户禁止调用
     */
    public function deserialize($param)
    {
        if ($param === null) {
            return;
        }
        if (array_key_exists("StartTime",$param) and $param["StartTime"] !== null) {
            $this->StartTime = $param["StartTime"];
        }

        if (array_key_exists("EndTime",$param) and $param["EndTime"] !== null) {
            $this->EndTime = $param["EndTime"];
        }

        if (array_key_exists("Granularity",$param) and $param["Granularity"] !== null) {
            $this->Granularity = $param["Granularity"];
        }

        if (array_key_exists("StatType",$param) and $param["StatType"] !== null) {
            $this->StatType = $param["StatType"];
        }

        if (array_key_exists("PlayDomains",$param) and $param["PlayDomains"] !== null) {
            $this->PlayDomains = $param["PlayDomains"];
        }
    }
}
