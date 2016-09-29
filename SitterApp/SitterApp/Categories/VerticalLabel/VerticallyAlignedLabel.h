//
//  VerticallyAlignedLabel.h
//
//  Created by Vikram Gour on 27/10/14.
//  Copyright (c) 2014 Sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef enum VerticalAlignment {
    VerticalAlignmentTop,
    VerticalAlignmentMiddle,
    VerticalAlignmentBottom,
} VerticalAlignment;

@interface VerticallyAlignedLabel : UILabel {
@private
    VerticalAlignment verticalAlignment_;
}

@property (nonatomic, assign) VerticalAlignment verticalAlignment;

- (void)autosizeForWidth:(int)width;

@end