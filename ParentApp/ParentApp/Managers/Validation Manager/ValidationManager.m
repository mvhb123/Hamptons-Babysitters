//
//  ValidationManager.m
//  MobShop
//
//  Created by Tushar Sarkar on 6/22/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import "ValidationManager.h"
#import "NSData+Base64.h"
#import "NSString+Helpers.h"
#import "Constants.h"

@implementation ValidationManager

static ValidationManager *sharedValidation = nil;

-(id)init{
    self = [super init];
    if (self) {
        //Allocate all variables
    }
    return self;
}

+(ValidationManager *)getInstance{
	@synchronized(self){
		if (sharedValidation == nil) {
			sharedValidation = [[self alloc] init];
		}
	}
	return sharedValidation;
}


-(BOOL)isValidEmailId:(NSString *)emailID{
	NSString *emailRegex = @"[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}"; 
	NSPredicate *emailTest = [NSPredicate predicateWithFormat:@"SELF MATCHES %@", emailRegex]; 
	return [emailTest evaluateWithObject:emailID];
}

-(BOOL)isValidUsername:(NSString *)username{
	BOOL isValid = NO;
	isValid = [self isEmptyString:username];
	return isValid;
}
-(BOOL)isValidName:(NSString *)name{
    BOOL isValid = NO;
    NSString *nameRegex = @"^[a-zA-Z][a-zA-Z\\s]+$";//@"[a-zA-z]+([ '-][a-zA-Z]+)*$";
    NSPredicate *emailTest = [NSPredicate predicateWithFormat:@"SELF MATCHES %@", nameRegex];
     isValid = [emailTest evaluateWithObject:name];
    return isValid;
    
}
-(BOOL)isValidPassword:(NSString *)password{
	BOOL isValid = NO;
	isValid = [self isEmptyString:password];
	return isValid;
}

-(BOOL)isBothStringSame:(NSString *)string1 :(NSString *)string2{
	BOOL isValid = NO;
	if ([string1 isEqualToString:string2]) {
		isValid = YES;
	}else {
		isValid = NO;
	}
	return isValid;
}
- (BOOL)validateZip:(NSString *)candidate {
    NSString *zipcodeRegex =@"^[0-9]{5}(-[0-9]{4})?$";
    NSPredicate *zipCodeTest = [NSPredicate predicateWithFormat:@"SELF MATCHES %@", zipcodeRegex];
    
    return [zipCodeTest evaluateWithObject:candidate];
}
-(BOOL)isValidCreditcard:(NSString *)cardNumber{
	BOOL isValid = NO;
	if ([self isEmptyString:cardNumber]) {
		cardNumber = [cardNumber stringByReplacingOccurrencesOfString:@" " withString:@""];
		cardNumber = [cardNumber stringByReplacingOccurrencesOfString:@"-" withString:@""];
		
		if (cardNumber.length < 10) {
			return isValid;
		}
		cardNumber = [self reverseString:cardNumber];
		NSInteger i = 0;
		NSInteger sum = 0;
		
		for (; i<=[cardNumber length]; i=i+2) {
			
			NSString *tempStr1 = @"";
			tempStr1 = [cardNumber substringFromIndex:i];
			if ([tempStr1 length]>0) {
				tempStr1 = [tempStr1 substringToIndex:1];
				sum = sum + [tempStr1 intValue];
			
			}
			if (i!=[cardNumber length]) {
				NSString *tempStr2 = @"";
				tempStr2 = [cardNumber substringFromIndex:(i+1)];
				if ([tempStr2 length]>0) {
					tempStr2 = [tempStr2 substringToIndex:1];
					NSInteger num = 0;
					num = [tempStr2 intValue] * 2;
					while (num>0) {
						NSInteger tempN = num % 10;
						num = num / 10;
						sum = sum + tempN;
					}
					
					
				}	
			}
		}
		if (sum != 0) {
			if (sum %10 == 0 ) {
				isValid = YES;
			}else {
				isValid = NO;
			}
		}else {
			isValid = NO;
		}
	
	
	}
	
	return isValid;
}

-(BOOL)isEmptyString:(NSString *)tempStr{
	BOOL isValid = NO;
//	tempStr = @"";
	if ([[tempStr stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceCharacterSet]] isEqualToString:@""]) {
		isValid = NO;
	}else {
		isValid = YES;
	}
	return isValid;
}



- (NSString *)reverseString:(NSString *)string {
    NSMutableString *reversedString = [[NSMutableString alloc] init];
    NSRange fullRange = [string rangeOfString:string];
    NSStringEnumerationOptions enumerationOptions = (NSStringEnumerationReverse | NSStringEnumerationByComposedCharacterSequences);
    [string enumerateSubstringsInRange:fullRange options:enumerationOptions usingBlock:^(NSString *substring, NSRange substringRange, NSRange enclosingRange, BOOL *stop) {
        [reversedString appendString:substring];
    }];
    return reversedString;
}

-(NSString *)decodeBase64EncodedString:(NSString *)base64Encoded
{
	NSData *tempData =  [NSData dataFromBase64String:base64Encoded];
	NSString *str = [[[NSString alloc] initWithData:tempData encoding:NSUTF8StringEncoding]autorelease];
	
	str = [str stringByReplacingOccurrencesOfString:@"\n" withString:@""];
	return str;
}


-(NSString *)encodeStringToBase64Encoded:(NSString *)stringToEncode{
	NSString *tempStr = [NSString stringWithFormat:@"%@",stringToEncode];
						 
	return [tempStr base64Encoding] ;
}


-(NSString *)encodeCreditCard:(NSDictionary *)cardDict{
	NSString *tempString;//Last 2 digit
	NSString *cardNo = [cardDict valueForKey:kCno];
	cardNo = [cardNo stringByReplacingOccurrencesOfString:@" " withString:@""];
	cardNo = [cardNo stringByReplacingOccurrencesOfString:@"-" withString:@""];
	NSString *cardNoFirstPart = [cardNo substringToIndex:9];
	NSString *cardNoSecondPart = [cardNo substringFromIndex:9];
	NSString *cardCVSNo =[NSString stringWithFormat:@"%@%@%@",[cardDict valueForKey:kCmon],[cardDict valueForKey:kCyear],[cardDict valueForKey:kCvno]];
	NSString *newCrdNo = [NSString stringWithFormat:@"%@%@",cardNoFirstPart,cardCVSNo];
	NSString *revNewCrdNo = [self reverseString:newCrdNo];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"0" withString:@"x"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"1" withString:@"r"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"2" withString:@"j"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"3" withString:@"l"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"4" withString:@"t"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"5" withString:@"q"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"6" withString:@"m"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"7" withString:@"n"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"8" withString:@"f"];
	cardNoSecondPart = [cardNoSecondPart stringByReplacingOccurrencesOfString:@"9" withString:@"k"];
	NSString *newStringToSend = [NSString stringWithFormat:@"%@%@",revNewCrdNo,cardNoSecondPart];
	tempString = [[self  reverseString:[self encodeStringToBase64Encoded:newStringToSend]] stringByReplacingOccurrencesOfString:@"=" withString:@"@"];
	
	return tempString;
}

- (NSString *) returnMD5Hash:(NSString*)concat {
    const char *concat_str = [concat UTF8String];
    unsigned char result[CC_MD5_DIGEST_LENGTH];
    CC_MD5(concat_str, strlen(concat_str), result);
    NSMutableString *hash = [NSMutableString string];
    for (int i = 0; i < 16; i++)
        [hash appendFormat:@"%02X", result[i]];
    return [hash lowercaseString];
	
}



@end
