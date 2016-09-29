//
//  NumberFormatter.h
//  NumberFormater
//
//  Created by Abhishek Jain on 4/29/15.
//  Copyright (c) 2015 Abhishek Jain. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "NBAsYouTypeFormatter.h"

@interface NumberFormatter : NSObject
@property (nonatomic, strong) NBAsYouTypeFormatter *numFormatter;
+(NumberFormatter *)getInstance;
- (id)initWithRegionCode:(NSString*)regionCode;
- (NSString *)formatText:(NSString *)text;
- (NSString *)deleteText;
-(NSString *)rawText:(NSString *)formatted;
@end
