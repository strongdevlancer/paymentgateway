package cz.monetplus.mips.eapi.v15.service;

import java.io.File;

import cz.monetplus.mips.eapi.v15.connector.entity.CustRes;
import cz.monetplus.mips.eapi.v15.connector.entity.EchoRes;
import cz.monetplus.mips.eapi.v15.connector.entity.PayRes;


public interface NativeApiV15Service {

	/**
	 * @param initFile
	 * @return  
	 */
	PayRes paymentInit(File initFile) throws MipsException;

	/**
	 * @param payId payment ID
	 * @return final URL to open payment in web browser 
	 */
	String paymentProcess(String payId) throws MipsException;

	/**
	 * @param payId payment ID
	 * @return
	 */
	PayRes paymentStatus(String payId) throws MipsException;

	/**
	 * @param payId payment ID
	 * @return
	 */
	PayRes paymentClose(String payId) throws MipsException;

	/**
	 * @param payId payment ID
	 * @return
	 */
	PayRes paymentReverse(String payId) throws MipsException;

	/**
	 * @param payId payment ID
	 * @return
	 */
	PayRes paymentRefund(String payId, Long amount) throws MipsException;

	/**
	 * @return
	 */
	EchoRes echoGet() throws MipsException;
	
	/**
	 * @return
	 */
	EchoRes echoPost() throws MipsException;

	/**
	 * @param customerId customer ID
	 * @return
	 */
	CustRes customerInfo(String customerId) throws MipsException;

	/**
	 * @param recurrentFile
	 * @return  
	 */
	PayRes paymentRecurrent(File recurrentFile) throws MipsException;

}
