﻿using CsobGatewayClientExample.Common;
using CsobGatewayClientExample.Communication;
using CsobGatewayClientExample.Communication.DataObjects;
using CsobGatewayClientExample.Communication.DataObjects.Act;
using CsobGatewayClientExample.Security;
using ManyConsole;
using NLog;

namespace CsobGatewayClientExample.Commands;

public class PaymentStatus : ConsoleCommand
{
    private readonly Logger _log = LogManager.GetCurrentClassLogger();

    public PaymentStatus()
    {
        IsCommand("PAYMENT_STATUS", "Performs PAYMENT_STATUS operation.");
        HasRequiredOption("p|payId=", "PayId obtained from PAYMENT_INIT", p => PayId = p);
    }

    private string? PayId { get; set; }

    public override int Run(string[] remainingArguments)
    {
        var crypto = new Crypto(Constants.MerchantId, Constants.MerchantKeyFileName, Constants.MipsPublicKey);
        var client = new MipsConnector(crypto);
        var result = client.PaymentStatus(PayId ?? throw new InvalidOperationException());
        _log.Info("result code: {}, result message: {}, payId: {}, paymentStatus: {}", result.ResultCode,
            result.ResultMessage, result.PayId, result.PaymentStatus);
        return 0;
    }
}