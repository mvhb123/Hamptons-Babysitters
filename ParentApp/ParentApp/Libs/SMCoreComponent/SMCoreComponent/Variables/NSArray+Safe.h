//
//  NSArray+Safe.h
//  SMCoreComponent
//
//  Created by Anubhav Saxena on 24/02/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSArray (Safe)
- (id)safeObjectAtIndex:(NSInteger)index;
@end
