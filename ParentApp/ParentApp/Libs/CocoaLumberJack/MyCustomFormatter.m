//
//  MyCustomFormatter.m
//  Boating App
//
//  Created by Abhishek on 3/16/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "MyCustomFormatter.h"

@implementation MyCustomFormatter

- (NSString *)formatLogMessage:(DDLogMessage *)logMessage {
//    NSString *logLevel;
//    switch (logMessage->_flag) {
//        case DDLogFlagError : logLevel = @"E"; break;
////        case DDLogFlagWarn  : logLevel = @"W"; break;
//        case DDLogFlagInfo  : logLevel = @"I"; break;
//        case DDLogFlagDebug : logLevel = @"D"; break;
//        default             : logLevel = @"V"; break;
//    }
    
    return [NSString stringWithFormat:@"%@ | %@ @ %@ | %@",
            [logMessage fileName], logMessage->_function, @(logMessage->_line), logMessage->_message];
}

@end