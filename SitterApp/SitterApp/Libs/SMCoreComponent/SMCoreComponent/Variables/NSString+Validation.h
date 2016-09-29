//
//  NSString+Validation.h
//  SMCoreComponent
//
//  Created by Tushar on 21/02/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSString (Validation)

-(BOOL) isValidEmail;
-(BOOL) containString:(NSString *)aString;
@end
