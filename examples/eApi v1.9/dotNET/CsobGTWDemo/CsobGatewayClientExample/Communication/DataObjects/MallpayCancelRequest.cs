using System.Text;
using Newtonsoft.Json;

namespace CsobGatewayClientExample.Communication.DataObjects;

public class MallpayCancelRequest : SignBaseRequest
{
    [JsonProperty("payId", Required = Required.Always)]
    public string? PayId { get; set; }

    [JsonProperty("reason", Required = Required.Always)]
    public string? Reason { get; set; }


    public override string ToSign()
    {
        var sb = new StringBuilder();
        Add(sb, MerchantId);
        Add(sb, PayId);
        Add(sb, Reason);
        Add(sb, Dttm);
        DeleteLast(sb);
        return sb.ToString();
    }
}