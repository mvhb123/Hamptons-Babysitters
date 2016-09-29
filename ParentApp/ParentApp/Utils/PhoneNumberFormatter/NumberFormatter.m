//
//  NumberFormatter.m
//  NumberFormater
//
//  Created by Abhishek Jain on 4/29/15.
//  Copyright (c) 2015 Abhishek Jain. All rights reserved.
//

#import "NumberFormatter.h"

@implementation NumberFormatter
+(NumberFormatter *)getInstance{
    static dispatch_once_t once;
    static NumberFormatter *sharedManager;
    dispatch_once(&once, ^ { sharedManager = [[self alloc] init]; });
    return sharedManager;
}

-(id)init{
    self = [super init];
    if (self) {
        //Allocate all variables
        
    }
    return self;
}

- (id)initWithRegionCode:(NSString*)regionCode
{
    self.numFormatter = [[NBAsYouTypeFormatter alloc] initWithRegionCode:@"US"];
    return [self init];
}
-(NSString *)formatText:(NSString *)text
{
    text = [[text componentsSeparatedByCharactersInSet:[[NSCharacterSet decimalDigitCharacterSet] invertedSet]]componentsJoinedByString:@""];
    [self.numFormatter clear];
    NSString *formatted;
    [self.numFormatter inputString:text];
    formatted = [self.numFormatter description];
    return formatted;
}

-(NSString *)deleteText
{
    NSString *formatted;
    [self.numFormatter removeLastDigit];
    formatted = [self.numFormatter description];
    return formatted;
}

-(NSString *)rawText:(NSString *)formatted
{
    NSString *text = [[formatted componentsSeparatedByCharactersInSet:[[NSCharacterSet decimalDigitCharacterSet] invertedSet]]componentsJoinedByString:@""];
    return text;
}
@end
