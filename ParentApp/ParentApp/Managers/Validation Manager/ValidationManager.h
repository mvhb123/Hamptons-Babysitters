//
//  ValidationManager.h
//  MobShop
//
//  Created by Tushar Sarkar on 6/22/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
//#import <Cocoa/Cocoa.h>
#import <CommonCrypto/CommonDigest.h>


@interface ValidationManager : NSObject {
	
}

+(ValidationManager *)getInstance;

- (BOOL)isValidEmailId:(NSString *)emailID;
- (BOOL)isValidUsername:(NSString *)username;
- (BOOL)isValidPassword:(NSString *)password;
-(BOOL)isValidName:(NSString *)name;
- (BOOL)isBothStringSame:(NSString *)string1 :(NSString *)string2;
- (BOOL)isValidCreditcard:(NSString *)cardNumber;
- (BOOL)isEmptyString:(NSString *)tempStr;
- (BOOL)validateZip:(NSString *)candidate;
- (NSString *)reverseString:(NSString *)string;
- (NSString *)decodeBase64EncodedString:(NSString *)base64Encoded;
- (NSString *)encodeStringToBase64Encoded:(NSString *)stringToEncode;
- (NSString *)encodeCreditCard:(NSDictionary *)cardDict;
- (NSString *) returnMD5Hash:(NSString*)concat;


@end
