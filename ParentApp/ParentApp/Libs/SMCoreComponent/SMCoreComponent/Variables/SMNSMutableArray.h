//
//  SMNSMutableArray.h
//  SMCoreComponent
//
//  Created by sofmen on 23/01/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface SMNSMutableArray : NSMutableArray
- (id)safeObjectAtIndex:(NSInteger)index;
- (void)insertSafeObject:(id)anObject atIndex:(NSUInteger)index;
@end
