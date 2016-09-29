//
//  NSDictionary+Safe.h
//  SMCoreComponent
//
//  Created by Shilpa Gade on 04/03/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSDictionary (Safe)
- (id)safeObjectForKey:(id)aKey;
@end
